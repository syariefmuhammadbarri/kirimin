<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentTracking;
use App\Models\User;
use App\Services\ShippingRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalkInBookingController extends Controller
{
    protected ShippingRateService $rateService;

    public function __construct(ShippingRateService $rateService)
    {
        $this->rateService = $rateService;
    }

    /**
     * FR-07: Form booking walk-in oleh admin cabang.
     */
    public function create()
    {
        $admin = Auth::user();
        $cities = \App\Models\City::orderBy('province')->orderBy('name')->get();
        $customers = Customer::with('user')->orderBy('created_at', 'desc')->get();

        return view('branch.booking_walkin', compact('cities', 'customers', 'admin'));
    }

    /**
     * Simpan booking walk-in yang dibuat admin cabang.
     * Walk-in: payment default cash pending (admin verifikasi saat penerimaan fisik paket).
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin_city'       => 'required|string',
            'destination_city'  => 'required|string',
            'sender_name'       => 'required|string|max:100',
            'sender_phone'      => 'required|string|max:20',
            'sender_address'    => 'required|string',
            'receiver_name'     => 'required|string|max:100',
            'receiver_phone'    => 'required|string|max:20',
            'receiver_address'  => 'required|string',
            'service_type'      => 'required|in:regular,express',
            'customer_id'       => 'nullable|exists:customers,id',
            // Items
            'items'             => 'required|array|min:1',
            'items.*.name'      => 'required|string|max:100',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.weight'    => 'required|numeric|min:0.1',
        ]);

        $admin = Auth::user();

        // Hitung berat estimasi
        $estimatedWeight = 0;
        foreach ($request->items as $item) {
            $estimatedWeight += $item['weight'] * $item['quantity'];
        }

        // Hitung harga
        $rateDetails = $this->rateService->calculate(
            $request->origin_city,
            $request->destination_city,
            $estimatedWeight,
            $request->service_type
        );

        return DB::transaction(function () use ($request, $admin, $estimatedWeight, $rateDetails) {
            // Resolve customer: pakai existing atau buat minimal dari data pengirim
            $customerId = $request->customer_id;

            if (!$customerId) {
                // Buat customer minimal dari nama & telepon pengirim (walk-in tanpa akun)
                // Cek dulu apakah ada customer dengan phone yg sama
                $existingCustomer = Customer::where('phone', $request->sender_phone)->first();
                if ($existingCustomer) {
                    $customerId = $existingCustomer->id;
                } else {
                    // Buat customer tanpa user (user_id null untuk walk-in anonim)
                    $walkinCustomer = Customer::create([
                        'user_id' => null,
                        'phone'   => $request->sender_phone,
                        'address' => $request->sender_address,
                        'city'    => $request->origin_city,
                    ]);
                    $customerId = $walkinCustomer->id;
                }
            }

            $today     = now()->format('Ymd');
            $randomStr = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $trackingNumber = 'EXP-' . $today . '-' . $randomStr;
            $bookingCode    = 'BK-' . $today . '-' . $randomStr;

            // Buat shipment — fulfillment_type selalu dropoff untuk walk-in
            $shipment = Shipment::create([
                'tracking_number'  => $trackingNumber,
                'booking_code'     => $bookingCode,
                'customer_id'      => $customerId,
                'branch_id'        => $admin->branch_id,
                'status'           => 'waiting_dropoff',
                'origin_city'      => $request->origin_city,
                'destination_city' => $request->destination_city,
                'sender_name'      => $request->sender_name,
                'sender_phone'     => $request->sender_phone,
                'sender_address'   => $request->sender_address,
                'receiver_name'    => $request->receiver_name,
                'receiver_phone'   => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'estimated_weight' => $estimatedWeight,
                'estimated_price'  => $rateDetails['total_price'],
                'total_price'      => $rateDetails['total_price'],
                'service_type'     => $request->service_type,
                'fulfillment_type' => 'dropoff',
            ]);

            // Items
            foreach ($request->items as $itemData) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'item_name'   => $itemData['name'],
                    'quantity'    => $itemData['quantity'],
                    'weight'      => $itemData['weight'],
                ]);
            }

            // Tracking awal
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location'    => $request->origin_city,
                'description' => 'Booking walk-in dibuat oleh Admin Cabang ' . ($admin->branch->name ?? '') . ' atas nama ' . $request->sender_name . '.',
                'status'      => 'waiting_dropoff',
                'tracked_at'  => now(),
            ]);

            // Payment — cash pending (akan diverifikasi nominal saat penerimaan fisik)
            $expiryHours = (int) Setting::getValue('booking_expiry_hours', 24);
            $payment = Payment::create([
                'order_id'       => $trackingNumber,
                'shipment_id'    => $shipment->id,
                'amount'         => $rateDetails['total_price'],
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'expired_at'     => now()->addHours($expiryHours * 7), // Walk-in: beri waktu lebih panjang (7x)
            ]);

            $shipment->update(['payment_id' => $payment->id]);

            Log::info("[WalkInBooking] Booking {$trackingNumber} dibuat oleh admin {$admin->name} (cabang {$admin->branch_id}).");

            return redirect()->route('branch.shipment.process', $shipment)
                ->with('success', "Booking walk-in {$bookingCode} berhasil dibuat. Selesaikan proses timbang dan konfirmasi pembayaran di bawah.");
        });
    }
}
