<?php

namespace App\Providers;

use App\Models\ShipmentTracking;
use App\Observers\ShipmentTrackingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // FR-08: Kirim notifikasi otomatis ke customer setiap status shipment berubah
        ShipmentTracking::observe(ShipmentTrackingObserver::class);
    }
}
