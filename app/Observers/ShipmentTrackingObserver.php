<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\ShipmentTracking;
use App\Notifications\ShipmentStatusChanged;
use Illuminate\Support\Facades\Log;

class ShipmentTrackingObserver
{
    /**
     * Setiap kali ShipmentTracking baru dibuat (status shipment berubah),
     * kirim notifikasi database ke customer pemilik shipment.
     */
    public function created(ShipmentTracking $tracking): void
    {
        $shipment = $tracking->shipment()->with('customer.user')->first();

        if (!$shipment) {
            return;
        }

        $customer = $shipment->customer;

        if (!$customer || !$customer->user) {
            return;
        }

        try {
            $customer->user->notify(new ShipmentStatusChanged(
                shipment: $shipment,
                status: $tracking->status,
                description: $tracking->description,
                location: $tracking->location,
            ));
        } catch (\Exception $e) {
            // Jangan biarkan gagal notifikasi mengganggu flow utama
            Log::warning("[ShipmentTrackingObserver] Gagal kirim notifikasi untuk shipment {$shipment->tracking_number}: " . $e->getMessage());
        }
    }
}
