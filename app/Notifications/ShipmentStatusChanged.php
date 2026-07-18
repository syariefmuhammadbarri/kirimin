<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShipmentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Shipment $shipment,
        public readonly string $status,
        public readonly string $description,
        public readonly string $location,
    ) {}

    /**
     * Kirim melalui channel database saja (persisten, bisa dibaca ulang).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'shipment_id'     => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'booking_code'    => $this->shipment->booking_code,
            'status'          => $this->status,
            'description'     => $this->description,
            'location'        => $this->location,
        ];
    }
}
