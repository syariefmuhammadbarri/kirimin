<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LandingContent;
use App\Models\Payment;
use App\Models\Rate;
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
                'phone' => '',
                'address' => '',
                'city' => '',
            ]);
        }

        $shipments = Shipment::with('payment')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('customer.dashboard', compact('shipments', 'customer'));
    }

    public function showBooking()
    {
        $cities = Rate::select('origin_city')->distinct()->pluck('origin_city');
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
            
            // Items details
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.weight' => 'required|numeric|min:0.1',
        ]);

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
        return DB::transaction(function () use ($request, $customer, $estimatedWeight, $rateDetails) {
            $today = now()->format('Ymd');
            $randomStr = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $trackingNumber = 'EXP-' . $today . '-' . $randomStr;
            $bookingCode = 'BK-' . $today . '-' . $randomStr;

            // 1. Create Shipment
            $shipment = Shipment::create([
                'tracking_number' => $trackingNumber,
                'booking_code' => $bookingCode,
                'customer_id' => $customer->id,
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
            $payment = Payment::create([
                'order_id' => $trackingNumber,
                'shipment_id' => $shipment->id,
                'amount' => $rateDetails['total_price'],
                'payment_method' => 'transfer',
                'payment_status' => 'pending',
            ]);

            $shipment->update(['payment_id' => $payment->id]);

            // Generate Snap Token
            $snapToken = $this->midtransService->getSnapToken($shipment);
            $payment->update(['snap_token' => $snapToken]);

            // Generate QR Code data (base64) for booking reference
            $qrCodeBase64 = $this->qrCodeService->generateBase64($bookingCode, 300);

            Log::info("Shipment Booking Successful: " . $trackingNumber);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Booking pengiriman berhasil dibuat. Silakan lakukan pembayaran.')
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

        // Generate snap token if empty or recreate it
        if (empty($payment->snap_token)) {
            $snapToken = $this->midtransService->getSnapToken($shipment);
            $payment->update(['snap_token' => $snapToken]);
        }

        return response()->json([
            'snap_token' => $payment->snap_token,
            'amount' => $payment->amount,
            'tracking_number' => $shipment->tracking_number,
        ]);
    }

    public function mockSettlePayment(Shipment $shipment)
    {
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

            $shipment->update([
                'status' => 'waiting_dropoff'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $shipment->origin_city,
                'description' => 'Pembayaran berhasil dikonfirmasi. Paket siap dibawa ke outlet terdekat.',
                'status' => 'waiting_dropoff',
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
