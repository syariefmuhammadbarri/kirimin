<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Customer;
use App\Models\DeliveryProof;
use App\Models\Rate;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShipmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $customerUser;
    protected Customer $customer;
    protected User $adminUser;
    protected User $courierUser;
    protected Branch $branchJakarta;
    protected Branch $branchBandung;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin_cabang', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kurir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        // 2. Settings
        Setting::setValue('midtrans_mock_mode', 'true');
        Setting::setValue('booking_expiry_hours', '24');
        Setting::setValue('max_delivery_attempts', '3');

        // 3. Branches
        $this->branchJakarta = Branch::create([
            'name' => 'Cabang Jakarta Pusat',
            'city' => 'Jakarta',
            'address' => 'Jl. Kebon Sirih No. 1',
            'phone' => '021-111111',
        ]);

        $this->branchBandung = Branch::create([
            'name' => 'Cabang Bandung Wetan',
            'city' => 'Bandung',
            'address' => 'Jl. Riau No. 10',
            'phone' => '022-222222',
        ]);

        // 4. Cities
        City::create(['name' => 'Jakarta', 'province' => 'DKI Jakarta']);
        City::create(['name' => 'Bandung', 'province' => 'Jawa Barat']);

        // 5. Rates (Rp 15.000 / kg for Jakarta -> Bandung)
        Rate::create([
            'origin_city' => 'Jakarta',
            'destination_city' => 'Bandung',
            'price_per_kg' => 15000,
            'estimated_days' => 2,
        ]);

        // 6. Users
        $this->customerUser = User::factory()->create([
            'name' => 'Customer Test',
            'email' => 'customer@gmail.com',
        ]);
        $this->customerUser->markEmailAsVerified();
        $this->customerUser->assignRole('customer');

        $this->customer = Customer::create([
            'user_id' => $this->customerUser->id,
            'phone' => '08123456789',
            'address' => 'Jl. Customer Jakarta',
            'city' => 'Jakarta',
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jakarta@ekspedisi.com',
            'branch_id' => $this->branchJakarta->id,
        ]);
        $this->adminUser->markEmailAsVerified();
        $this->adminUser->assignRole('admin_cabang');

        $this->courierUser = User::factory()->create([
            'name' => 'Courier Jakarta',
            'email' => 'courier.jakarta@ekspedisi.com',
            'branch_id' => $this->branchJakarta->id,
        ]);
        $this->courierUser->markEmailAsVerified();
        $this->courierUser->assignRole('kurir');
    }

    /**
     * Skenario 1: Customer Minta Dijemput Kurir (Pickup Workflow)
     */
    public function test_pickup_shipment_workflow(): void
    {
        Storage::fake('public');

        // 1. Customer creates booking with fulfillment "pickup"
        $this->actingAs($this->customerUser);

        $bookingData = [
            'sender_name' => 'Pengirim Test',
            'sender_phone' => '08123456789',
            'sender_address' => 'Jl. Kebon Sirih No 10',
            'origin_city' => 'Jakarta',
            'receiver_name' => 'Penerima Test',
            'receiver_phone' => '08987654321',
            'receiver_address' => 'Jl. Asia Afrika No 5',
            'destination_city' => 'Bandung',
            'fulfillment_type' => 'pickup',
            'pickup_address' => 'Jl. Kebon Sirih No 10',
            'pickup_scheduled_at' => now()->addHours(5)->format('Y-m-d\TH:i'),
            'service_type' => 'regular',
            'items' => [
                [
                    'name' => 'Sepatu',
                    'quantity' => 1,
                    'weight' => 2.0,
                ]
            ],
        ];

        $response = $this->post(route('customer.booking.store'), $bookingData);
        $response->assertRedirect();

        $shipment = Shipment::first();
        $this->assertNotNull($shipment);
        $this->assertStringStartsWith('EXP-', $shipment->tracking_number);
        $this->assertStringStartsWith('BK-', $shipment->booking_code);
        $this->assertEquals('booking_created', $shipment->status);
        $this->assertEquals('pickup', $shipment->fulfillment_type);

        // 2. Customer settles payment via Midtrans Mock Mode -> changes status to pickup_scheduled
        $payResponse = $this->post(route('customer.payment.mock-settle', $shipment));
        $payResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('paid', $shipment->payment->payment_status);
        $this->assertEquals('pickup_scheduled', $shipment->status);

        // 3. Admin Cabang assigns pickup courier
        $this->actingAs($this->adminUser);

        $assignPickupResponse = $this->post(route('branch.assign-pickup-courier', $shipment), [
            'courier_id' => $this->courierUser->id,
        ]);
        $assignPickupResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('pickup_assigned', $shipment->status);
        $this->assertEquals($this->courierUser->id, $shipment->courier_id);

        // 4. Courier collects shipment from customer
        $this->actingAs($this->courierUser);

        $collectResponse = $this->post(route('courier.collect', $shipment));
        $collectResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('picked_up_from_customer', $shipment->status);

        // 5. Courier drops shipment at branch
        $dropResponse = $this->post(route('courier.drop-at-branch', $shipment));
        $dropResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('received_at_branch', $shipment->status);
        $this->assertNull($shipment->courier_id, 'Courier ID should reset to null after dropping at branch');

        // 6. Admin assigns delivery courier
        $this->actingAs($this->adminUser);

        $assignDeliveryResponse = $this->post(route('branch.assign-courier', $shipment), [
            'courier_id' => $this->courierUser->id,
        ]);
        $assignDeliveryResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('assigned_to_courier', $shipment->status);
        $this->assertEquals($this->courierUser->id, $shipment->courier_id);

        // 7. Courier picks up package from branch for delivery
        $this->actingAs($this->courierUser);

        $pickupBranchResponse = $this->post(route('courier.pickup', $shipment));
        $pickupBranchResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('picked_up', $shipment->status);

        // 8. Courier goes out for delivery
        $outForDeliveryResponse = $this->post(route('courier.out-for-delivery', $shipment));
        $outForDeliveryResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('out_for_delivery', $shipment->status);

        // 9. Courier completes delivery with proof of delivery (POD)
        $completeResponse = $this->post(route('courier.deliver', $shipment), [
            'recipient_name' => 'Budi Penerima',
            'recipient_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'photos' => [
                UploadedFile::fake()->image('bukti1.jpg'),
            ],
            'notes' => 'Diterima oleh Budi langsung',
        ]);
        $completeResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('delivery_confirmation_pending', $shipment->status);
        $this->assertNotNull($shipment->deliveryProof);

        // 10. Admin verifies and accepts delivery proof
        $this->actingAs($this->adminUser);

        $proof = $shipment->deliveryProof;
        $acceptResponse = $this->post(route('branch.delivery-confirmations.accept', $proof));
        $acceptResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
    }

    /**
     * Skenario 2: Customer Drop-Off dengan Batas Retry & Return-to-Sender Workflow
     */
    public function test_dropoff_shipment_with_retry_limit_workflow(): void
    {
        // 1. Customer creates booking with fulfillment "dropoff"
        $this->actingAs($this->customerUser);

        $bookingData = [
            'sender_name' => 'Dropoff Sender',
            'sender_phone' => '08123456789',
            'sender_address' => 'Jl. Sudirman 10',
            'origin_city' => 'Jakarta',
            'receiver_name' => 'Dropoff Receiver',
            'receiver_phone' => '08987654321',
            'receiver_address' => 'Jl. Merdeka 5',
            'destination_city' => 'Bandung',
            'fulfillment_type' => 'dropoff',
            'service_type' => 'regular',
            'items' => [
                [
                    'name' => 'Buku',
                    'quantity' => 2,
                    'weight' => 1.5,
                ]
            ],
        ];

        $response = $this->post(route('customer.booking.store'), $bookingData);
        $response->assertRedirect();

        $shipment = Shipment::first();
        $this->assertEquals('booking_created', $shipment->status);

        // 2. Customer settles payment online
        $this->post(route('customer.payment.mock-settle', $shipment));
        $shipment->refresh();
        $this->assertEquals('waiting_dropoff', $shipment->status);

        // 3. Admin scans shipment at branch
        $this->actingAs($this->adminUser);

        $scanResponse = $this->post(route('branch.scan.process'), [
            'booking_code' => $shipment->booking_code,
        ]);
        $scanResponse->assertRedirect(route('branch.shipment.process', $shipment));

        // 4. Admin weighs shipment
        $weighResponse = $this->post(route('branch.process-weigh', $shipment), [
            'actual_weight' => 2.0,
        ]);
        $weighResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('received_at_branch', $shipment->status);

        // 5. Admin assigns delivery courier
        $this->post(route('branch.assign-courier', $shipment), [
            'courier_id' => $this->courierUser->id,
        ]);

        $shipment->refresh();
        $this->assertEquals('assigned_to_courier', $shipment->status);

        // 6. Courier picks up & goes out for delivery
        $this->actingAs($this->courierUser);

        $this->post(route('courier.pickup', $shipment));
        $shipment->refresh();

        $this->post(route('courier.out-for-delivery', $shipment));
        $shipment->refresh();
        $this->assertEquals('out_for_delivery', $shipment->status);

        // 7. Delivery Fail #1 -> Retry #1 (attempt count becomes 1, status out_for_delivery)
        $fail1 = $this->post(route('courier.fail', $shipment), ['reason' => 'Rumah kosong 1']);
        $fail1->assertRedirect();
        $shipment->refresh();
        $this->assertEquals('gagal_kirim', $shipment->status);

        $retry1 = $this->post(route('courier.retry', $shipment));
        $retry1->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(1, $shipment->delivery_attempt_count);
        $this->assertEquals('out_for_delivery', $shipment->status);

        // 8. Delivery Fail #2 -> Retry #2 (attempt count becomes 2, status out_for_delivery)
        $fail2 = $this->post(route('courier.fail', $shipment), ['reason' => 'Rumah kosong 2']);
        $fail2->assertRedirect();
        $shipment->refresh();
        $this->assertEquals('gagal_kirim', $shipment->status);

        $retry2 = $this->post(route('courier.retry', $shipment));
        $retry2->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(2, $shipment->delivery_attempt_count);
        $this->assertEquals('out_for_delivery', $shipment->status);

        // 9. Delivery Fail #3 -> Retry #3 (attempt count becomes 3, max attempts reached -> status automatically returned)
        $fail3 = $this->post(route('courier.fail', $shipment), ['reason' => 'Rumah kosong 3']);
        $fail3->assertRedirect();
        $shipment->refresh();
        $this->assertEquals('gagal_kirim', $shipment->status);

        $retry3 = $this->post(route('courier.retry', $shipment));
        $retry3->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(3, $shipment->delivery_attempt_count);
        $this->assertEquals('returned', $shipment->status);
    }

    /**
     * Skenario 3: Booking Walk-In oleh Admin Cabang & Pembayaran Tunai
     */
    public function test_walk_in_booking_cash_payment_workflow(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Admin creates walk-in booking
        $walkInData = [
            'sender_name' => 'Pelanggan Walkin',
            'sender_phone' => '08555555555',
            'sender_address' => 'Jl. Loket Cabang 1',
            'origin_city' => 'Jakarta',
            'receiver_name' => 'Penerima Walkin',
            'receiver_phone' => '08777777777',
            'receiver_address' => 'Jl. Tujuan 2',
            'destination_city' => 'Bandung',
            'service_type' => 'regular',
            'items' => [
                [
                    'name' => 'Dokumen Penting',
                    'quantity' => 1,
                    'weight' => 2.0, // 2kg * Rp 15.000 = Rp 30.000
                ]
            ],
        ];

        $response = $this->post(route('branch.booking.walkin.store'), $walkInData);
        $response->assertRedirect();

        $shipment = Shipment::where('sender_name', 'Pelanggan Walkin')->first();
        $this->assertNotNull($shipment);
        $this->assertEquals('waiting_dropoff', $shipment->status);
        $this->assertEquals('cash', $shipment->payment->payment_method);
        $this->assertEquals('pending', $shipment->payment->payment_status);
        $this->assertEquals(30000, $shipment->total_price);

        // 2. Admin processes cash payment with Rp 50.000 cash (Total Rp 30.000 -> Change Rp 20.000)
        $paymentResponse = $this->post(route('branch.payment.process', $shipment), [
            'amount_paid' => 50000,
        ]);
        $paymentResponse->assertRedirect();

        $shipment->refresh();
        $this->assertEquals('paid', $shipment->payment->payment_status);
        $this->assertEquals(50000, $shipment->payment->paid_amount);
        $this->assertEquals(20000, $shipment->payment->paid_amount - $shipment->total_price);
        $this->assertEquals('received_at_branch', $shipment->status);
    }
}
