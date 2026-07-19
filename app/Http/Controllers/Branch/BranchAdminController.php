<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierAssignment;
use App\Models\DeliveryProof;
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

    public function dashboard(Request $request)
    {
        $admin = Auth::user();
        $branchId = $admin->branch_id;
        
        if (!$branchId) {
            return redirect()->route('landing')->with('error', 'Akun Anda tidak memiliki asosiasi cabang.');
        }

        $branch = Branch::findOrFail($branchId);

        // Fetch shipments that originated, are currently at this branch, or are heading to this branch
        $query = Shipment::with(['customer', 'payment', 'courier', 'nextBranch'])
            ->where(function ($q) use ($branchId, $branch) {
                $q->where('branch_id', $branchId)
                  ->orWhere('origin_city', $branch->city)
                  ->orWhere('next_branch_id', $branchId);
            });

        // Add filter support if requested in the dashboard view
        $statusFilter = $request->input('status');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $allShipments = (clone $query)->get();

        $couriers = User::role('kurir')
            ->where('branch_id', $branchId)
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $allShipments->count(),
            'waiting_dropoff' => $allShipments->where('status', 'waiting_dropoff')->count(),
            'pickup_scheduled' => $allShipments->where('status', 'pickup_scheduled')->count(),
            'pickup_assigned' => $allShipments->where('status', 'pickup_assigned')->count(),
            'weighed' => $allShipments->where('status', 'weighed')->count(),
            'received' => $allShipments->where('status', 'received_at_branch')->count(),
            'assigned' => $allShipments->where('status', 'assigned_to_courier')->count(),
            'transit' => $allShipments->whereIn('status', ['in_transit', 'picked_up', 'out_for_delivery', 'picked_up_from_customer'])->count(),
            'delivered' => $allShipments->where('status', 'delivered')->count(),
        ];

        $shipments = $query->latest()->paginate(10);
        $branches = Branch::all();

        return view('branch.dashboard', compact('branch', 'shipments', 'couriers', 'stats', 'branches'));
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

        $shipment = Shipment::where('booking_code', $code)
            ->orWhere('tracking_number', $code)
            ->first();

        if (!$shipment) {
            return back()->with('error', 'Kode Booking atau Nomor Resi tidak ditemukan.');
        }

        return redirect()->route('branch.shipment.process', $shipment);
    }

    public function processPage(Shipment $shipment)
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        // Validate branch assignment (origin city should match admin's branch city, or update branch_id)
        if (empty($shipment->branch_id)) {
            $shipment->update(['branch_id' => $admin->branch_id]);
        }

        $shipment->load(['customer', 'items', 'payment']);

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

            // Notify customer
            $shipment->notifyStatusChange($status, $desc, $branch->city);
        });

        return redirect()->route('branch.shipment.process', $shipment)->with('success', 'Paket berhasil ditimbang dan diproses.');
    }

    public function confirmCashPayment(Request $request, Shipment $shipment)
    {
        // FR-02: Pickup wajib bayar online — cash hanya untuk dropoff
        if ($shipment->fulfillment_type === 'pickup') {
            return back()->with('error', 'Paket dengan layanan jemput (pickup) tidak dapat dibayar tunai di outlet.');
        }

        // FR-03: Wajib input nominal uang diterima
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        $payment = $shipment->payment;
        if (!$payment) {
            abort(404);
        }

        // Validasi nominal tidak boleh kurang dari tagihan
        if ((float) $request->paid_amount < (float) $shipment->total_price) {
            return back()->with('error', 'Nominal yang diterima (Rp ' . number_format($request->paid_amount, 0, ',', '.') . ') kurang dari tagihan (Rp ' . number_format($shipment->total_price, 0, ',', '.') . ').');
        }

        DB::transaction(function () use ($request, $shipment, $payment, $branch) {
            $payment->update([
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'paid_amount'    => $request->paid_amount,
            ]);

            // Transition status
            $shipment->update([
                'status' => 'received_at_branch'
            ]);

            $kembalian = (float) $request->paid_amount - (float) $shipment->total_price;

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location'    => $branch->city,
                'description' => 'Pembayaran tunai diterima di outlet. Nominal: Rp ' . number_format($request->paid_amount, 0, ',', '.') . '. Kembalian: Rp ' . number_format($kembalian, 0, ',', '.') . '. Paket masuk gudang cabang.',
                'status'      => 'received_at_branch',
                'tracked_at'  => now(),
            ]);
        });

        return redirect()->route('branch.shipment.process', $shipment)->with('success', 'Pembayaran cash berhasil dikonfirmasi. Kembalian: Rp ' . number_format((float) $request->paid_amount - (float) $shipment->total_price, 0, ',', '.') . '.');
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

        // FR-05: Validasi kurir harus berasal dari cabang yang sama dengan admin yang meng-assign
        if ($courier->branch_id !== $admin->branch_id) {
            return back()->with('error', 'Kurir ' . $courier->name . ' tidak terdaftar di cabang ' . $branch->name . '. Pilih kurir dari cabang yang sama.');
        }

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

        // Validasi status berdasarkan fulfillment_type
        $isPickup = $shipment->fulfillment_type === 'pickup';
        if ($isPickup && $shipment->status !== 'pickup_scheduled') {
            return back()->with('error', 'Paket pickup tidak dalam status menunggu penugasan kurir jemput.');
        }
        if (!$isPickup && $shipment->status !== 'received_at_branch') {
            return back()->with('error', 'Paket dropoff harus sudah diterima di cabang (received_at_branch) sebelum ditugaskan ke kurir.');
        }

        DB::transaction(function () use ($request, $shipment, $courier, $admin, $branch, $isPickup) {
            $newStatus = $isPickup ? 'pickup_assigned' : 'assigned_to_courier';
            $assignmentType = $isPickup ? 'pickup' : 'delivery';
            $description = $isPickup
                ? "Kurir {$courier->name} ditugaskan untuk menjemput paket di alamat pengirim."
                : "Paket ditugaskan ke Kurir {$courier->name} untuk pengantaran. " . ($request->notes ? "Catatan: {$request->notes}" : '');

            // Update shipment
            $shipment->update([
                'courier_id' => $courier->id,
                'status' => $newStatus,
            ]);

            // Create assignment record
            CourierAssignment::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $courier->id,
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'status' => 'assigned',
                'type' => $assignmentType,
                'notes' => $request->notes,
            ]);

            // Create tracking
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => $description,
                'status' => $newStatus,
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.shipment.process', $shipment)->with('success', "Kurir {$courier->name} berhasil ditugaskan untuk paket {$shipment->tracking_number}.");
    }

    public function viewAssignments()
    {
        $admin = Auth::user();
        $branchId = $admin->branch_id;

        if (!$branchId) {
            return redirect()->route('landing')->with('error', 'Akun Anda tidak memiliki asosiasi cabang.');
        }

        $branch = Branch::findOrFail($branchId);

        // Ambil shipment yang VALID untuk ditugaskan ke kurir:
        // Tipe PICKUP yang baru di-booking ATAU Tipe DROPOFF yang sudah lunas/ada di cabang (received_at_branch)
        $shipments = Shipment::with(['customer', 'payment'])
            ->where('branch_id', $branchId)
            ->where(function($query) {
                $query->where('fulfillment_type', 'pickup')->where('status', 'pickup_scheduled')
                      ->orWhere('fulfillment_type', 'dropoff')->where('status', 'received_at_branch');
            })
            ->get();

        // Terapkan Aturan BR-10: Hanya ambil kurir aktif dari cabang yang sama dengan Admin
        $availableCouriers = User::role('kurir')
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        // Existing assignments history
        $assignments = CourierAssignment::with(['shipment', 'courier', 'assignor'])
            ->whereHas('shipment', function ($query) use ($branchId, $branch) {
                $query->where('branch_id', $branchId)
                      ->orWhere('origin_city', $branch->city);
            })
            ->latest()
            ->get();

        $stats = [
            'total' => $assignments->count(),
            'active' => $assignments->whereIn('status', ['pending', 'assigned'])->count(),
            'completed' => $assignments->where('status', 'completed')->count(),
            'cancelled' => $assignments->where('status', 'cancelled')->count(),
        ];

        return view('branch.assignments', compact('shipments', 'availableCouriers', 'assignments', 'stats', 'branch'));
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

    public function sendTransit(Request $request, Shipment $shipment)
    {
        $request->validate([
            'next_branch_id' => 'required|exists:branches,id|different:branch_id',
        ]);

        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);
        $nextBranch = Branch::findOrFail($request->next_branch_id);

        if (!in_array($shipment->status, ['received_at_branch', 'weighed'])) {
            return back()->with('error', 'Status paket tidak valid untuk dikirim transit.');
        }

        DB::transaction(function () use ($shipment, $branch, $nextBranch) {
            $shipment->update([
                'status' => 'in_transit',
                'next_branch_id' => $nextBranch->id,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => "Paket diberangkatkan dari Cabang {$branch->name} menuju Cabang {$nextBranch->name} ({$nextBranch->city}).",
                'status' => 'in_transit',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.shipment.process', $shipment)->with('success', "Paket dikirim transit menuju {$nextBranch->name}.");
    }

    public function receiveTransit(Shipment $shipment)
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        if ($shipment->next_branch_id !== $branch->id) {
            return back()->with('error', 'Paket ini tidak dijadwalkan transit ke cabang Anda.');
        }

        DB::transaction(function () use ($shipment, $branch) {
            $isFinalDestination = strtolower($branch->city) === strtolower($shipment->destination_city);

            $shipment->update([
                'status' => 'received_at_branch',
                'branch_id' => $branch->id,
                'next_branch_id' => null,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => $isFinalDestination
                    ? "Paket transit telah tiba di Cabang {$branch->name} (cabang tujuan akhir)."
                    : "Paket transit telah tiba di Cabang {$branch->name} (transit intermediate).",
                'status' => 'received_at_branch',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.dashboard')->with('success', 'Paket transit berhasil diterima di cabang ini.');
    }

    public function assignPickupCourier(Request $request, Shipment $shipment)
    {
        $request->validate([
            'courier_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($shipment->status !== 'pickup_scheduled') {
            return back()->with('error', 'Paket ini tidak sedang menunggu penjemputan.');
        }

        $courier = User::findOrFail($request->courier_id);
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        // Check courier availability: max 5 active assignments (pickup + delivery)
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
            $shipment->update([
                'courier_id' => $courier->id,
                'status' => 'pickup_assigned',
                'branch_id' => $branch->id,
            ]);

            CourierAssignment::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $courier->id,
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'status' => 'assigned',
                'type' => 'pickup',
                'notes' => $request->notes,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch->city,
                'description' => "Kurir {$courier->name} ditugaskan untuk menjemput paket di alamat pengirim.",
                'status' => 'pickup_assigned',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('branch.shipment.process', $shipment)->with('success', "Kurir {$courier->name} ditugaskan untuk menjemput paket.");
    }

    // =====================================================
    // DELIVERY CONFIRMATION — Accept / Reject by Admin
    // =====================================================

    /**
     * Menampilkan daftar konfirmasi pengiriman yang menunggu persetujuan admin.
     */
    public function deliveryConfirmations()
    {
        $admin = Auth::user();
        $branch = Branch::findOrFail($admin->branch_id);

        $pendingConfirmations = DeliveryProof::with(['shipment', 'courier'])
            ->where('admin_status', 'pending')
            ->whereHas('shipment', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->latest()
            ->get();

        $acceptedConfirmations = DeliveryProof::with(['shipment', 'courier'])
            ->where('admin_status', 'accepted')
            ->whereHas('shipment', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->latest()
            ->get();

        return view('branch.delivery_confirmations', compact('pendingConfirmations', 'acceptedConfirmations', 'branch'));
    }

    /**
     * Accept delivery confirmation — ubah status shipment menjadi "delivered"
     */
    public function acceptDelivery(Request $request, DeliveryProof $deliveryProof)
    {
        $admin = Auth::user();

        if ($deliveryProof->admin_status !== 'pending') {
            return back()->with('error', 'Konfirmasi ini sudah diproses sebelumnya.');
        }

        $shipment = $deliveryProof->shipment;
        if (!$shipment) {
            abort(404);
        }

        DB::transaction(function () use ($deliveryProof, $shipment, $admin) {
            $deliveryProof->update([
                'admin_status' => 'accepted',
                'reviewed_by'  => $admin->id,
                'reviewed_at'  => now(),
            ]);

            $shipment->update(['status' => 'delivered']);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location'    => $shipment->destination_city,
                'description' => "Admin cabang menyetujui konfirmasi pengiriman. Paket resmi terkirim ke {$deliveryProof->recipient_name}.",
                'status'      => 'delivered',
                'tracked_at'  => now(),
            ]);

            // Notify customer
            $shipment->notifyStatusChange('delivered', "Paket berhasil terkirim ke {$deliveryProof->recipient_name}. Terima kasih telah menggunakan Kirimin.", $shipment->destination_city);
        });

        return redirect()->route('branch.delivery-confirmations')->with('success', 'Konfirmasi pengiriman diterima. Status paket berubah menjadi "Terkirim".');
    }

    /**
     * Reject delivery confirmation — kembalikan status ke out_for_delivery untuk perbaikan kurir
     */
    public function rejectDelivery(Request $request, DeliveryProof $deliveryProof)
    {
        $admin = Auth::user();

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        if ($deliveryProof->admin_status !== 'pending') {
            return back()->with('error', 'Konfirmasi ini sudah diproses sebelumnya.');
        }

        $shipment = $deliveryProof->shipment;
        if (!$shipment) {
            abort(404);
        }

        DB::transaction(function () use ($request, $deliveryProof, $shipment, $admin) {
            $deliveryProof->update([
                'admin_status' => 'rejected',
                'admin_notes'  => $request->reject_reason,
                'reviewed_by'  => $admin->id,
                'reviewed_at'  => now(),
            ]);

            $shipment->update([
                'status' => 'out_for_delivery',
                'courier_id' => $deliveryProof->courier_id,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location'    => $shipment->destination_city,
                'description' => "Admin cabang menolak konfirmasi pengiriman. Alasan: {$request->reject_reason}. Kurir diminta memperbaiki konfirmasi.",
                'status'      => 'out_for_delivery',
                'tracked_at'  => now(),
            ]);

            // Notify customer
            $shipment->notifyStatusChange('out_for_delivery', "Konfirmasi pengiriman ditolak admin. Alasan: {$request->reject_reason}. Kurir akan memperbaiki.", $shipment->destination_city);
        });

        return redirect()->route('branch.delivery-confirmations')->with('success', 'Konfirmasi ditolak. Paket dikembalikan ke status pengantaran.');
    }
}
