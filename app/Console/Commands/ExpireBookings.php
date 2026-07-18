<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';
    protected $description = 'Expire bookings that have not been paid within time limit';

    public function handle()
    {
        $expired = Shipment::whereIn('status', ['booking_created', 'payment_pending'])
            ->whereHas('payment', function ($q) {
                $q->where('payment_status', 'pending')
                  ->where('expired_at', '<', now());
            })
            ->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancel_reason' => 'Booking expired']);

        $this->info("Expired {$expired} bookings.");
    }
}