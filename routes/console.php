<?php

use App\Console\Commands\ExpireBookings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// FR-04: Expire booking yang belum dibayar setiap jam
Schedule::command(ExpireBookings::class)->hourly();
