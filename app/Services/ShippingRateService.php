<?php

namespace App\Services;

use App\Models\Rate;

class ShippingRateService
{
    /**
     * Calculate cost based on route, weight and service type
     */
    /**
     * Normalize city name by stripping "KOTA " or "KABUPATEN " prefix for rate lookup.
     */
    private function normalizeCityName(string $city): string
    {
        $normalized = trim(strtolower($city));
        // Remove common prefixes like "kota ", "kabupaten " 
        $prefixes = ['kota ', 'kabupaten '];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $normalized = substr($normalized, strlen($prefix));
                break;
            }
        }
        return trim($normalized);
    }

    public function calculate(string $origin, string $destination, float $weight, string $serviceType = 'regular'): array
    {
        $originClean = $this->normalizeCityName($origin);
        $destinationClean = $this->normalizeCityName($destination);

        // Find rate — match both raw and normalized city names
        $rate = Rate::where(function($q) use ($originClean, $origin) {
                $q->whereRaw('LOWER(origin_city) = ?', [$originClean])
                  ->orWhereRaw('LOWER(origin_city) = ?', [trim(strtolower($origin))]);
            })
            ->where(function($q) use ($destinationClean, $destination) {
                $q->whereRaw('LOWER(destination_city) = ?', [$destinationClean])
                  ->orWhereRaw('LOWER(destination_city) = ?', [trim(strtolower($destination))]);
            })
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
