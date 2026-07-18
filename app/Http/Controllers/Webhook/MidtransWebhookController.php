<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received: ' . json_encode($payload));

        // 1. Verify Signature
        if (!$this->midtransService->verifyWebhookSignature($payload)) {
            Log::warning('Midtrans Webhook Signature Verification Failed!');
            return response()->json(['message' => 'Invalid signature key'], 400);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        // Map Midtrans payment type to our enum
        $paymentMethodMap = [
            'credit_card' => 'transfer',
            'bank_transfer' => 'transfer',
            'echannel' => 'transfer',
            'bca_klikpay' => 'transfer',
            'bca_klikbca' => 'transfer',
            'cimb_clicks' => 'transfer',
            'gopay' => 'e-wallet',
            'shopeepay' => 'e-wallet',
            'qris' => 'e-wallet',
            'indomaret' => 'cash',
            'alfamart' => 'cash',
            'akulaku' => 'transfer',
        ];
        $mappedPaymentMethod = $paymentMethodMap[$paymentType] ?? 'midtrans';

        // Find the payment record
        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            Log::warning('Payment record not found for Order ID: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $shipment = Shipment::find($payment->shipment_id);
        if (!$shipment) {
            Log::warning('Shipment not found for Payment ID: ' . $payment->id);
            return response()->json(['message' => 'Shipment not found'], 404);
        }

        DB::transaction(function () use ($payment, $shipment, $transactionStatus, $mappedPaymentMethod, $fraudStatus) {
            $status = 'pending';
            $shipmentStatus = $shipment->status;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $status = 'paid';
                    $shipmentStatus = $shipment->fulfillment_type === 'pickup' ? 'pickup_scheduled' : 'waiting_dropoff';
                }
            } else if ($transactionStatus == 'settlement') {
                $status = 'paid';
                // If weight was already adjusted, keeping it as weighed, else waiting_dropoff or pickup_scheduled
                if ($shipmentStatus === 'booking_created' || $shipmentStatus === 'payment_pending') {
                    $shipmentStatus = $shipment->fulfillment_type === 'pickup' ? 'pickup_scheduled' : 'waiting_dropoff';
                }
            } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $status = 'failed';
            } else if ($transactionStatus == 'pending') {
                $status = 'pending';
            }

            // Update payment record
            $payment->update([
                'payment_status' => $status,
                'payment_method' => Payment::normalizePaymentMethod($mappedPaymentMethod),
            ]);

            // If payment was settled, update shipment status and create timeline tracking checkpoint
            if ($status === 'paid') {
                $shipment->update(['status' => $shipmentStatus]);

                $description = $shipment->fulfillment_type === 'pickup'
                    ? 'Pembayaran lunas via Midtrans. Menunggu penjemputan oleh kurir.'
                    : 'Pembayaran lunas via Midtrans. Status berubah menjadi Siap Drop-Off.';

                // Create tracking timeline update
                ShipmentTracking::create([
                    'shipment_id' => $shipment->id,
                    'location' => $shipment->origin_city,
                    'description' => $description,
                    'status' => $shipmentStatus,
                    'tracked_at' => now(),
                ]);

                Log::info("Payment Settled for Order: {$payment->order_id}. Shipment: {$shipment->tracking_number}");
            } elseif ($status === 'failed') {
                $shipment->update(['status' => 'cancelled']);

                ShipmentTracking::create([
                    'shipment_id' => $shipment->id,
                    'location' => $shipment->origin_city,
                    'description' => 'Transaksi pembayaran dibatalkan atau kedaluwarsa. Pemesanan gagal.',
                    'status' => 'cancelled',
                    'tracked_at' => now(),
                ]);

                Log::info("Payment Failed for Order: {$payment->order_id}");
            }
        });

        return response()->json(['message' => 'Notification processed successfully']);
    }
}
