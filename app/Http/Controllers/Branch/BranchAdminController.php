<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierAssignment;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\User;
use App\Services\ReportService;
use App\Services\ShippingRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchAdminController extends Controller
{
    protected ShippingRateService $rateService;
    protected ReportService $reportService;

    public function __construct(ShippingRateService $rateService, ReportService $reportService)
    {
        $this->rateService = $rateService;
        $this->reportService = $reportService;
    }

    public function dashboard()
    {
        $admin = Auth::user();
        $branchId = $admin->branch_id;
        
        if (!$branchId) {
            return redirect()->route('landing')->with('error', 'Akun Anda tidak memiliki asosiasi cabang.');
        }

        $branch = Branch::findOrFail($branchId);

        // Fetch shipments that originated or are currently at this branch
        $shipments = Shipment::with(['customer', 'payment', 'courier'])
            ->where(function ($query) use ($branchId, $branch) {
                $query->where('branch_id', $branchId)
                      ->orWhere('origin_city', $branch->city);
            })
            ->latest()
            ->paginate(10);

        $couriers = User::role('kurir')
            ->where('branch_id', $branchId)
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $shipments->count(),
            'waiting_dropoff' => $shipments->where('status', 'waiting_dropoff')->count(),
            'weighed' => $shipments->where('status', 'weighed')->count(),
            'received' => $shipments->where('status', 'received_at_branch')->count(),
            'assigned' => $shipments->where('status', 'assigned_to_courier')->count(),
            'transit' => $shipments->whereIn('status', ['in_transit', 'picked_up', 'out_for_delivery'])->count(),
            'delivered' => $shipments->where('status', 'delivered')->count(),
        ];

        return view('branch.dashboard', compact('branch', 'shipments', 'couriers', 'stats'));
    }

    public function showScan()
    {
        return view('branch.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string',
        ]);

        $code = $request->booking_code;
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        $shipment = Shipment::with(['customer', 'items', 'payment'])
            ->where('booking_code', $code)
            ->orWhere('tracking_number', $code)
            ->first();

        if (!$shipment) {
            return back()->with('error', 'Kode Booking atau Nomor Resi tidak ditemukan.');
        }

        // Validate branch assignment (origin city should match admin's branch city, or update branch_id)
        if (empty($shipment->branch_id)) {
            $shipment->update(['branch_id' => $admin->branch_id]);
        }

        return view('branch.process_booking', compact('shipment', 'branch'));
    }

    public function processWeigh(Request $request, Shipment $shipment)
    {
        $request->validate([
            'actual_weight' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        DB::transaction(function () use ($request, $shipment, $branch) {
            $actualWeight = $request->actual_weight;

            // Recalculate cost
            $rateDetails = $this->rateService->calculate(
                $shipment->origin_city,
                $shipment->destination_city,
                $actualWeight,
                $shipment->service_type
            );

            $oldPrice = $shipment->total_price;
            $newPrice = $rateDetails['total_price'];
            $payment = $shipment->payment;
            
            $status = 'weighed';

            if ($payment) {
                $payment->update(['amount' => $newPrice]);

                if ($newPrice > $oldPrice && $payment->payment_status === 'paid') {
                    // Payment needs upgrade/re-pay difference
                    $payment->update(['payment_status' => 'pending']);
                    $status = 'payment_pending';
                } elseif ($payment->payment_status === 'paid') {
                    // Already paid and price is same or cheaper
                    $status = 'received_at_branch';
                }
            }

            // Update shipment
            $shipment->update([
                'actual_weight' => $actualWeight,
                'actual_price' => $newPrice,
                'total_price' => $newPrice,
                'status' => $status,
                'branch_id' => $branch->id,
            ]);

            // Set tracking description based on status
            if ($status === 'received_at_branch') {
                $desc = "Paket ditimbang di outlet {$branch->name}. Berat Aktual: {$actualWeight} kg. Pembayaran lunas, paket masuk gudang cabang.";
            } elseif ($status === 'payment_pending') {
                $desc = "Paket ditimbang di outlet {$branch->name}. Berat Aktual: {$actualWeight} kg. Ada selisih tarif yang harus dibayar, menunggu kekurangan pembayaran.";
            } else {
                $desc = "Paket ditimbang di outlet {$branch->name}. Berat Aktual: {$actualWeight} kg. Catatan: " . ($request->notes ?: '-');
            }

            // Create tracking update
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => $desc,
                'status' => $status,
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.dashboard')->with('success', 'Paket berhasil ditimbang dan diproses.');
    }

    public function confirmCashPayment(Shipment $shipment)
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        $payment = $shipment->payment;
        if (!$payment) {
            abort(404);
        }

        DB::transaction(function () use ($shipment, $payment, $branch) {
            $payment->update([
                'payment_status' => 'paid',
                'payment_method' => 'cash'
            ]);

            // Transition status
            $shipment->update([
                'status' => 'received_at_branch'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => 'Pembayaran tunai diterima di outlet. Paket masuk gudang cabang.',
                'status' => 'received_at_branch',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Pembayaran cash berhasil dikonfirmasi.');
    }

    public function assignCourier(Request $request, Shipment $shipment)
    {
        $request->validate([
            'courier_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $courier = User::findOrFail($request->courier_id);
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        // Check courier availability: max 5 active assignments
        $activeCount = CourierAssignment::where('courier_id', $courier->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->count();

        if ($activeCount >= 5) {
            return back()->with('error', 'Kurir ini sudah memiliki ' . $activeCount . ' tugas aktif. Maksimal 5 tugas per kurir.');
        }

        // Check if shipment already has an active assignment
        $existingAssignment = CourierAssignment::where('shipment_id', $shipment->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->first();

        if ($existingAssignment) {
            return back()->with('error', 'Paket ini sudah memiliki penugasan kurir aktif.');
        }

        DB::transaction(function () use ($request, $shipment, $courier, $admin, $branch) {
            // Update shipment
            $shipment->update([
                'courier_id' => $courier->id,
                'status' => 'assigned_to_courier'
            ]);

            // Create assignment record
            CourierAssignment::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $courier->id,
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'status' => 'assigned',
                'notes' => $request->notes,
            ]);

            // Create tracking
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => "Paket ditugaskan ke Kurir {$courier->name} untuk pengantaran. " . ($request->notes ? "Catatan: {$request->notes}" : ''),
                'status' => 'assigned_to_courier',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', "Kurir {$courier->name} berhasil ditugaskan untuk paket {$shipment->tracking_number}.");
    }

    public function viewAssignments()
    {
        $admin = Auth::user();
        $branchId = $admin->branch_id;

        if (!$branchId) {
            return redirect()->route('landing')->with('error', 'Akun Anda tidak memiliki asosiasi cabang.');
        }

        $branch = Branch::findOrFail($branchId);

        $assignments = CourierAssignment::with(['shipment', 'courier', 'assignor'])
            ->whereHas('shipment', function ($query) use ($branchId, $branch) {
                $query->where('branch_id', $branchId)
                      ->orWhere('origin_city', $branch->city);
            })
            ->latest()
            ->get();

        $couriers = User::role('kurir')
            ->where('branch_id', $branchId)
            ->get();

        $stats = [
            'total' => $assignments->count(),
            'active' => $assignments->whereIn('status', ['pending', 'assigned'])->count(),
            'completed' => $assignments->where('status', 'completed')->count(),
            'cancelled' => $assignments->where('status', 'cancelled')->count(),
        ];

        return view('branch.assignments', compact('assignments', 'couriers', 'stats', 'branch'));
    }

    public function printReceipt(Shipment $shipment)
    {
        $admin = Auth::user();
        if ($shipment->branch_id !== $admin->branch_id) {
            abort(403, 'Akses ditolak. Paket bukan milik cabang Anda.');
        }

        $pdf = $this->reportService->generateReceipt($shipment);
        return $pdf->download('resi-' . $shipment->tracking_number . '.pdf');
    }

    public function downloadBranchReport()
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        $shipments = Shipment::with(['customer', 'payment'])
            ->where('branch_id', $branch->id)
            ->get();

        $metrics = [
            'total_revenue' => $shipments->where('payment.payment_status', 'paid')->sum('total_price'),
            'total_shipments' => $shipments->count(),
            'delivery_success_rate' => $shipments->count() > 0 
                ? ($shipments->where('status', 'delivered')->count() / $shipments->count()) * 100 
                : 100.0,
            'branches' => [
                [
                    'name' => $branch->name,
                    'shipments_count' => $shipments->count(),
                    'revenue' => $shipments->where('payment.payment_status', 'paid')->sum('total_price'),
                ]
            ],
            'couriers' => [],
            'status_distribution' => $shipments->groupBy('status')->map->count()->toArray()
        ];

        // Fetch branch couriers performance
        $couriers = User::role('kurir')
            ->where('branch_id', $branch->id)
            ->get();

        foreach ($couriers as $courier) {
            $totalJobs = Shipment::where('courier_id', $courier->id)->count();
            $deliveredJobs = Shipment::where('courier_id', $courier->id)->where('status', 'delivered')->count();
            $metrics['couriers'][] = [
                'name' => $courier->name,
                'branch_name' => $branch->name,
                'total_jobs' => $totalJobs,
                'delivered_jobs' => $deliveredJobs,
                'success_rate' => $totalJobs > 0 ? ($deliveredJobs / $totalJobs) * 100 : 100.0
            ];
        }

        $pdf = $this->reportService->generateStrategicReport($metrics);
        return $pdf->download('laporan-cabang-' . str_replace(' ', '-', strtolower($branch->name)) . '.pdf');
    }

    public function sendTransit(Shipment $shipment)
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        if (!in_array($shipment->status, ['received_at_branch', 'weighed'])) {
            return back()->with('error', 'Status paket tidak valid untuk dikirim transit.');
        }

        DB::transaction(function () use ($shipment, $branch) {
            $shipment->update([
                'status' => 'in_transit'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => "Paket diberangkatkan dari Cabang {$branch->name} (transit) menuju kota tujuan {$shipment->destination_city}.",
                'status' => 'in_transit',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Paket berhasil dikirim (Transit).');
    }

    public function receiveTransit(Shipment $shipment)
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        DB::transaction(function () use ($shipment, $branch) {
            $shipment->update([
                'status' => 'received_at_branch',
                'branch_id' => $branch->id
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => "Paket transit telah tiba dan diterima di Cabang {$branch->name}.",
                'status' => 'received_at_branch',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.dashboard')->with('success', 'Paket transit berhasil diterima di cabang ini.');
    }
}
