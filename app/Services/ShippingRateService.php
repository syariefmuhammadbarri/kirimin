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
        $prefixes = ['kota ', 'kabupaten '];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $normalized = substr($normalized, strlen($prefix));
                break;
            }
        }
        return trim($normalized);
    }

    private function resolveHubCityName(string $city): string
    {
        $cityClean = trim(strtolower($city));
        $branches = \App\Models\Branch::all();

        // 1. Direct or keyword match against branch cities
        foreach ($branches as $b) {
            $bCityClean = trim(strtolower($b->city));
            if ($bCityClean === $cityClean || str_contains($cityClean, $bCityClean)) {
                return $b->city;
            }
        }

        // 2. Province fallback
        $cleanWithoutPrefix = str_replace(['kota ', 'kabupaten '], '', $cityClean);
        $originCityRecord = \App\Models\City::where(function($q) use ($cityClean, $cleanWithoutPrefix) {
            $q->whereRaw('LOWER(name) = ?', [$cityClean])
              ->orWhereRaw('LOWER(REPLACE(REPLACE(name, "KOTA ", ""), "KABUPATEN ", "")) = ?', [$cleanWithoutPrefix]);
        })->first();

        if ($originCityRecord && $originCityRecord->province) {
            $province = $originCityRecord->province;
            $provinceCityNames = \App\Models\City::where('province', $province)
                ->pluck('name')
                ->map(fn($n) => trim(strtolower($n)))
                ->toArray();

            foreach ($branches as $b) {
                $bCityClean = trim(strtolower($b->city));
                foreach ($provinceCityNames as $pName) {
                    if ($bCityClean === $pName || str_contains($pName, $bCityClean)) {
                        return $b->city;
                    }
                }
            }
        }

        return $city;
    }

    public function calculate(string $origin, string $destination, float $weight, string $serviceType = 'regular'): array
    {
        $originClean = $this->normalizeCityName($origin);
        $destinationClean = $this->normalizeCityName($destination);

        $originHub = $this->resolveHubCityName($origin);
        $destinationHub = $this->resolveHubCityName($destination);

        $originCandidates = array_unique([
            trim(strtolower($origin)),
            $originClean,
            trim(strtolower($originHub))
        ]);

        $destinationCandidates = array_unique([
            trim(strtolower($destination)),
            $destinationClean,
            trim(strtolower($destinationHub))
        ]);

        // Find rate across all candidate city representations
        $rate = Rate::whereIn(\DB::raw('LOWER(origin_city)'), $originCandidates)
            ->whereIn(\DB::raw('LOWER(destination_city)'), $destinationCandidates)
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

    /**
     * Suggest the next branch hop from current branch to final destination branch using simple BFS.
     */
    public function suggestNextHop(\App\Models\Branch $current, \App\Models\Branch $destination): ?\App\Models\Branch
    {
        if ($current->id === $destination->id) {
            return null;
        }

        $routes = \App\Models\BranchRoute::where('is_active', true)->get();

        $adj = [];
        foreach ($routes as $route) {
            $adj[$route->from_branch_id][] = $route->to_branch_id;
            $adj[$route->to_branch_id][] = $route->from_branch_id;
        }

        $start = $current->id;
        $target = $destination->id;

        if (!isset($adj[$start]) || !isset($adj[$target])) {
            return null;
        }

        $queue = new \SplQueue();
        $queue->enqueue([$start]);
        $visited = [$start => true];

        while (!$queue->isEmpty()) {
            $path = $queue->dequeue();
            $node = end($path);

            if ($node === $target) {
                if (isset($path[1])) {
                    return \App\Models\Branch::find($path[1]);
                }
                return null;
            }

            foreach ($adj[$node] ?? [] as $neighbor) {
                if (!isset($visited[$neighbor])) {
                    $visited[$neighbor] = true;
                    $newPath = $path;
                    $newPath[] = $neighbor;
                    $queue->enqueue($newPath);
                }
            }
        }

        return null;
    }
}

