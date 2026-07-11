<?php

namespace App\Services;

use App\Models\Shipment;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function getSnapToken(Shipment $shipment): string
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $mockMode = filter_var(env('MIDTRANS_MOCK_MODE', true), FILTER_VALIDATE_BOOLEAN);

        if ($mockMode || empty($serverKey) || $serverKey === 'mock_server_key') {
            Log::info("Midtrans in mock mode. Returning mock snap token for: " . $shipment->tracking_number);
            return 'mock_snap_token_' . $shipment->tracking_number;
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
            \Midtrans\Config::$isSanitized = filter_var(env('MIDTRANS_IS_SANITIZED', true), FILTER_VALIDATE_BOOLEAN);
            \Midtrans\Config::$is3ds = filter_var(env('MIDTRANS_IS_3DS', true), FILTER_VALIDATE_BOOLEAN);

            $customerEmail = $shipment->customer->email ?? 'customer@ekspedisi.com';

            $params = [
                'transaction_details' => [
                    'order_id' => $shipment->tracking_number,
                    'gross_amount' => (int) $shipment->total_price,
                ],
                'customer_details' => [
                    'first_name' => $shipment->sender_name,
                    'phone' => $shipment->sender_phone,
                    'email' => $customerEmail,
                    'billing_address' => [
                        'first_name' => $shipment->sender_name,
                        'phone' => $shipment->sender_phone,
                        'address' => $shipment->sender_address,
                        'city' => $shipment->origin_city,
                    ],
                    'shipping_address' => [
                        'first_name' => $shipment->receiver_name,
                        'phone' => $shipment->receiver_phone,
                        'address' => $shipment->receiver_address,
                        'city' => $shipment->destination_city,
                    ],
                ],
                'item_details' => [
                    [
                        'id' => $shipment->tracking_number,
                        'price' => (int) $shipment->total_price,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim ' . ucfirst($shipment->service_type) . ' (' . $shipment->tracking_number . ')',
                    ]
                ],
            ];

            return \Midtrans\Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Midtrans Snap Generation Failed: ' . $e->getMessage());
            // Fallback to mock token in case of API failure so the app doesn't break
            return 'mock_snap_token_' . $shipment->tracking_number;
        }
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        if (empty($serverKey) || $serverKey === 'mock_server_key') {
            return true; // Auto-pass in mock mode
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($localSignature, $signatureKey);
    }
}
