<?php

namespace App\Services;

use App\Models\Rate;

class ShippingRateService
{
    /**
     * Calculate cost based on route, weight and service type
     */
    public function calculate(string $origin, string $destination, float $weight, string $serviceType = 'regular'): array
    {
        $originClean = trim(strtolower($origin));
        $destinationClean = trim(strtolower($destination));

        // Find rate
        $rate = Rate::whereRaw('LOWER(origin_city) = ?', [$originClean])
            ->whereRaw('LOWER(destination_city) = ?', [$destinationClean])
            ->first();

        // Default base rate and days if not found
        $pricePerKg = $rate ? (float) $rate->price_per_kg : 12000.00;
        $estimatedDays = $rate ? (int) $rate->estimated_days : 3;

        // Express service is 1.5x regular price and arrives 1 day faster (minimum 1 day)
        if (strtolower($serviceType) === 'express') {
            $pricePerKg = $pricePerKg * 1.5;
            $estimatedDays = max(1, $estimatedDays - 1);
        }

        $totalPrice = $pricePerKg * $weight;

        return [
            'price_per_kg' => $pricePerKg,
            'estimated_days' => $estimatedDays,
            'total_price' => $totalPrice,
            'route_found' => !is_null($rate)
        ];
    }
}
