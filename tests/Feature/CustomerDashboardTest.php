<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_dashboard_can_render_without_customer_profile(): void
    {
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->markEmailAsVerified();
        $user->assignRole('customer');

        $this->actingAs($user);

        $response = $this->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertViewHas('customer', function ($customer) use ($user) {
            return $customer instanceof Customer
                && $customer->user_id === $user->id;
        });

        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
        ]);
    }
}
