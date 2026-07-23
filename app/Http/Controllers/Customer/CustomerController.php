<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Models\Customer;
use App\Models\LandingContent;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentTracking;
use App\Services\MidtransService;
use App\Services\QRCodeService;
use App\Services\ReportService;
use App\Services\ShippingRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected ShippingRateService $rateService;
    protected MidtransService $midtransService;
    protected ReportService $reportService;
    protected QRCodeService $qrCodeService;

    public function __construct(
        ShippingRateService $rateService,
        MidtransService $midtransService,
        ReportService $reportService,
        QRCodeService $qrCodeService
    ) {
        $this->rateService = $rateService;
        $this->midtransService = $midtransService;
        $this->reportService = $reportService;
        $this->qrCodeService = $qrCodeService;
    }

    public function landing()
    {
        $branches = \App\Models\Branch::all();
        $cities = Rate::select('origin_city')->distinct()->pluck('origin_city');
        $landingContents = LandingContent::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('section');
        return view('landing', compact('branches', 'cities', 'landingContents'));
    }

    public function calculator()
    {
        $cities = Rate::select('origin_city')->distinct()->pluck('origin_city');
        return view('calculator', compact('cities'));
    }

    public function branches()
    {
        $branches = \App\Models\Branch::all();
        return view('branches', compact('branches'));
    }

    public function trackPublic(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');
        $shipment = null;
        
        if ($trackingNumber) {
            $shipment = Shipment::with('trackings')
                ->where('tracking_number', $trackingNumber)
                ->orWhere('booking_code', $trackingNumber)
                ->first();
        }

        return view('tracking', compact('shipment', 'trackingNumber'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'phone'   => '',
                'address' => '',
                'city'    => '',
            ]);
        }

        $shipments = Shipment::with('payment')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        // Summary stat cards — single query aggregation
        $allShipments = Shipment::where('customer_id', $customer->id)->get();
        $stats = [
            'total'          => $allShipments->count(),
            'pending_payment'=> $allShipments->whereIn('status', ['booking_created', 'payment_pending'])->count(),
            'in_progress'    => $allShipments->whereNotIn('status', ['booking_created', 'payment_pending', 'delivered', 'cancelled', 'returned'])->count(),
            'delivered'      => $allShipments->where('status', 'delivered')->count(),
        ];

        return view('customer.dashboard', compact('shipments', 'customer', 'stats'));
    }

    public function showBooking()
    {
        // Ambil semua kota terurut untuk dropdown
        $citiesRaw = City::orderBy('province')->orderBy('name')->get();

        // Tandai setiap kota dengan flag is_serviced (apakah kota ini atau provinsinya dilayani cabang)
        $cities = $citiesRaw->map(function ($city) {
            $city->is_serviced = $this->resolveBranchForCity($city->name) !== null;
            return $city;
        });

        return view('customer.booking', compact('cities'));
    }

    public function calculateRate(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
            'service_type' => 'required|string|in:regular,express',
        ]);

        $calc = $this->rateService->calculate(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->service_type
        );

        return response()->json($calc);
    }

    /**
     * Helper: resolve branch_id from origin_city.
     * FR-U1/BR-18: branch_id wajib terisi saat booking.
     * Uses direct match, then province fallback via cities table.
     */
    private function resolveBranchForCity(string $city): ?int
    {
        $cityClean = trim(strtolower($city));
        $branches = Branch::all();

        // 1. Direct match or keyword match
        foreach ($branches as $b) {
            $bCityClean = trim(strtolower($b->city));
            if ($bCityClean === $cityClean || str_contains($cityClean, $bCityClean)) {
                return $b->id;
            }
        }

        // 2. Fallback to branch in the same province
        $originCityRecord = City::where(function($q) use ($cityClean) {
            $cleanWithoutPrefix = str_replace(['kota ', 'kabupaten '], '', $cityClean);
            $q->whereRaw('LOWER(name) = ?', [$cityClean])
              ->orWhereRaw('LOWER(REPLACE(REPLACE(name, "KOTA ", ""), "KABUPATEN ", "")) = ?', [$cleanWithoutPrefix]);
        })->first();

        if ($originCityRecord && $originCityRecord->province) {
            $province = $originCityRecord->province;
            $provinceCityNames = City::where('province', $province)
                ->pluck('name')
                ->map(fn($n) => trim(strtolower($n)))
                ->toArray();

            foreach ($branches as $b) {
                $bCityClean = trim(strtolower($b->city));
                foreach ($provinceCityNames as $pName) {
                    if ($bCityClean === $pName || str_contains($pName, $bCityClean)) {
                        return $b->id;
                    }
                }
            }
        }

        return null;
    }


    public function createBooking(Request $request)
    {
        $request->validate([
            'origin_city' => 'required|string',
            'destination_city' => 'required|string',
            'sender_name' => 'required|string|max:100',
            'sender_phone' => 'required|string|max:20',
            'sender_address' => 'required|string',
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string',
            'service_type' => 'required|string|in:regular,express',
            'fulfillment_type' => 'required|in:dropoff,pickup',
            'pickup_address' => 'required_if:fulfillment_type,pickup|nullable|string',
            'pickup_scheduled_at' => 'required_if:fulfillment_type,pickup|nullable|date|after:now',
            'pickup_notes' => 'nullable|string|max:500',
            
            // Items details
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.weight' => 'required|numeric|min:0.1',
        ]);

        // FR-U2: Validasi cabang melayani origin_city
        $branchId = $this->resolveBranchForCity($request->origin_city);
        if (!$branchId) {
            return redirect()->back()->withInput()->with('error', 'Maaf, kota asal "' . $request->origin_city . '" belum memiliki cabang kami. Silakan pilih kota lain.');
        }

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'phone' => '',
                'address' => '',
                'city' => '',
            ]);
        }

        // Calculate total weight estimated
        $estimatedWeight = 0;
        foreach ($request->items as $item) {
            $estimatedWeight += $item['weight'] * $item['quantity'];
        }

        // Calculate pricing
        $rateDetails = $this->rateService->calculate(
            $request->origin_city,
            $request->destination_city,
            $estimatedWeight,
            $request->service_type
        );

        // Run in Transaction to avoid double bookings or partial rows
        return DB::transaction(function () use ($request, $customer, $estimatedWeight, $rateDetails, $branchId) {
            $today = now()->format('Ymd');
            $randomStr = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $trackingNumber = 'EXP-' . $today . '-' . $randomStr;
            $bookingCode = 'BK-' . $today . '-' . $randomStr;

            // 1. Create Shipment — branch_id langsung diisi (BR-18)
            $shipment = Shipment::create([
                'tracking_number' => $trackingNumber,
                'booking_code' => $bookingCode,
                'customer_id' => $customer->id,
                'branch_id' => $branchId,
                'status' => 'booking_created',
                'origin_city' => $request->origin_city,
                'destination_city' => $request->destination_city,
                'sender_name' => $request->sender_name,
                'sender_phone' => $request->sender_phone,
                'sender_address' => $request->sender_address,
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'estimated_weight' => $estimatedWeight,
                'estimated_price' => $rateDetails['total_price'],
                'total_price' => $rateDetails['total_price'],
                'service_type' => $request->service_type,
                'fulfillment_type' => $request->fulfillment_type,
                'pickup_address' => $request->fulfillment_type === 'pickup' ? $request->pickup_address : null,
                'pickup_scheduled_at' => $request->fulfillment_type === 'pickup' ? $request->pickup_scheduled_at : null,
                'pickup_notes' => $request->pickup_notes,
            ]);

            // 2. Create Items
            foreach ($request->items as $itemData) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'item_name' => $itemData['name'],
                    'quantity' => $itemData['quantity'],
                    'weight' => $itemData['weight'],
                ]);
            }

            // 3. Create Timeline tracking record
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $request->origin_city,
                'description' => 'Booking pengiriman dibuat oleh pengirim.',
                'status' => 'booking_created',
                'tracked_at' => now(),
            ]);

            // 4. Create Payment transaction entry
            $expiryHours = (int) Setting::getValue('booking_expiry_hours', 24);
            $payment = Payment::create([
                'order_id'       => $trackingNumber,
                'shipment_id'    => $shipment->id,
                'amount'         => $rateDetails['total_price'],
                'payment_method' => 'transfer',
                'payment_status' => 'pending',
                'expired_at'     => now()->addHours($expiryHours),
            ]);

            $shipment->update(['payment_id' => $payment->id]);

            // Generate Snap Token (hanya untuk online payment)
            $snapToken = $this->midtransService->getSnapToken($shipment);
            $payment->update(['snap_token' => $snapToken]);

            // Generate QR Code data (base64) for booking reference
            $qrCodeBase64 = $this->qrCodeService->generateBase64($bookingCode, 300);

            Log::info("Shipment Booking Successful: " . $trackingNumber);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Booking pengiriman berhasil dibuat! Selesaikan pembayaran dalam ' . $expiryHours . ' jam.')
                ->with('qr_code', $qrCodeBase64)
                ->with('booking_code', $bookingCode);
        });
    }

    public function paymentDetails(Shipment $shipment)
    {
        // Enforce owner check
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || $shipment->customer_id !== $customer->id) {
            abort(403);
        }

        $payment = $shipment->payment;
        if (!$payment) {
            abort(404);
        }

        // Auto-sync status with Midtrans API if currently unpaid
        if ($payment->payment_status !== 'paid') {
            $this->midtransService->syncPaymentStatus($shipment);
            $payment->refresh();
            $shipment->refresh();
        }

        // Generate snap token if empty or recreate it
        if (empty($payment->snap_token)) {
            $snapToken = $this->midtransService->getSnapToken($shipment);
            $payment->update(['snap_token' => $snapToken]);
        }

        return response()->json([
            'snap_token' => $payment->snap_token,
            'amount' => (float) $payment->amount,
            'formatted_amount' => 'Rp ' . number_format($payment->amount, 0, ',', '.'),
            'tracking_number' => $shipment->tracking_number,
            'booking_code' => $shipment->booking_code ?? $shipment->tracking_number,
            'sender_name' => $shipment->sender_name,
            'receiver_name' => $shipment->receiver_name,
            'origin_city' => $shipment->origin_city,
            'destination_city' => $shipment->destination_city,
            'service_type' => strtoupper($shipment->service_type),
            'weight' => $shipment->weight,
            'fulfillment_type' => $shipment->fulfillment_type,
            'payment_status' => $payment->payment_status,
            'shipment_status' => $shipment->status,
            'client_key' => config('services.midtrans.client_key'),
            'mock_mode' => (bool) config('services.midtrans.mock_mode', false),
        ]);
    }

    public function finishPayment(Request $request, Shipment $shipment)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || $shipment->customer_id !== $customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $synced = $this->midtransService->syncPaymentStatus($shipment);
        $status = $request->input('transaction_status') ?? $request->input('result.transaction_status');

        if ($synced || in_array($status, ['settlement', 'capture', 'success'])) {
            $payment = $shipment->payment;
            if ($payment && $payment->payment_status !== 'paid') {
                DB::transaction(function () use ($payment, $shipment) {
                    $payment->update([
                        'payment_status' => 'paid',
                        'payment_method' => Payment::normalizePaymentMethod('midtrans')
                    ]);
                    $newStatus = $shipment->fulfillment_type === 'pickup' ? 'pickup_scheduled' : 'waiting_dropoff';
                    $shipment->update(['status' => $newStatus]);

                    $description = $shipment->fulfillment_type === 'pickup'
                        ? 'Pembayaran lunas via Midtrans. Menunggu penjemputan oleh kurir.'
                        : 'Pembayaran lunas via Midtrans. Status berubah menjadi Siap Drop-Off.';

                    ShipmentTracking::create([
                        'shipment_id' => $shipment->id,
                        'location' => $shipment->origin_city,
                        'description' => $description,
                        'status' => $newStatus,
                        'tracked_at' => now(),
                    ]);
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi.',
                'payment_status' => 'paid',
                'shipment_status' => $shipment->fresh()->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran diperbarui.',
            'payment_status' => $shipment->payment->fresh()->payment_status ?? 'pending',
            'shipment_status' => $shipment->fresh()->status,
        ]);
    }

    public function syncPayment(Shipment $shipment)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || $shipment->customer_id !== $customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $synced = $this->midtransService->syncPaymentStatus($shipment);

        return response()->json([
            'success' => $synced,
            'payment_status' => $shipment->payment->fresh()->payment_status ?? 'pending',
            'shipment_status' => $shipment->fresh()->status,
            'message' => $synced ? 'Status pembayaran berhasil diperbarui ke Lunas!' : 'Pembayaran belum terdeteksi Lunas di Midtrans.'
        ]);
    }

    public function mockSettlePayment(Shipment $shipment)
    {
        // Guard: hanya tersedia di lingkungan development/testing
        if (!app()->environment('local', 'testing')) {
            abort(403, 'Fitur simulasi pembayaran hanya tersedia di lingkungan pengembangan.');
        }

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || $shipment->customer_id !== $customer->id) {
            abort(403);
        }

        $payment = $shipment->payment;
        if (!$payment) {
            abort(404);
        }

        DB::transaction(function () use ($shipment, $payment) {
            $payment->update([
                'payment_status' => 'paid',
                'payment_method' => Payment::normalizePaymentMethod('midtrans')
            ]);

            $newStatus = $shipment->fulfillment_type === 'pickup' ? 'pickup_scheduled' : 'waiting_dropoff';

            $shipment->update([
                'status' => $newStatus
            ]);

            $description = $shipment->fulfillment_type === 'pickup'
                ? 'Pembayaran berhasil dikonfirmasi. Menunggu kurir ditugaskan untuk penjemputan paket.'
                : 'Pembayaran berhasil dikonfirmasi. Paket siap dibawa ke outlet terdekat.';

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $shipment->origin_city,
                'description' => $description,
                'status' => $newStatus,
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Simulasi pembayaran berhasil! Status pembayaran ter-update.');
    }

    public function downloadInvoice(Shipment $shipment)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || $shipment->customer_id !== $customer->id) {
            abort(403);
        }

        $pdf = $this->reportService->generateInvoice($shipment);
        return $pdf->download('invoice-' . $shipment->tracking_number . '.pdf');
    }
}
