<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Rate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Branch::create([
            'name' => 'Cabang Utama',
            'city' => 'Jakarta',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        Rate::create([
            'origin_city' => 'Jakarta',
            'destination_city' => 'Bandung',
            'price_per_kg' => 10000,
            'estimated_days' => 2,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Layanan Pengiriman Terpercaya');
    }
}
