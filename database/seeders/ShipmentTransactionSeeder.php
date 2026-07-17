<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryProof;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentTracking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ShipmentTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $customerUser = User::where('email', 'customer@gmail.com')->first();
        $customer = Customer::where('user_id', $customerUser->id)->first();
        $branches = Branch::all();
        $couriers = User::role('kurir')->get();

        if ($branches->count() < 1 || $couriers->count() < 1) {
            $this->command->warn('Branches or Couriers not found. Skipping transactional seeder.');
            return;
        }

        $branchJkt = $branches->firstWhere('city', 'Jakarta');
        $branchBdg = $branches->firstWhere('city', 'Bandung');
        $courierJkt = $couriers->firstWhere('branch_id', $branchJkt?->id);
        $courierBdg = $couriers->firstWhere('branch_id', $branchBdg?->id);

        // ===== 1. BOOKING CREATED (Pending Payment) =====
        $shipment1 = Shipment::create([
            'tracking_number' => 'EXP-20260717-ABCD1',
            'booking_code' => 'BK-20260717-ABCD1',
            'customer_id' => $customer->id,
            'branch_id' => $branchJkt?->id,
            'status' => 'booking_created',
            'origin_city' => 'Jakarta',
            'destination_city' => 'Bandung',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Budi Penerima',
            'receiver_phone' => '082345678901',
            'receiver_address' => 'Jl. Riau No. 10, Bandung',
            'estimated_weight' => 2.00,
            'actual_weight' => null,
            'estimated_price' => 20000,
            'actual_price' => null,
            'total_price' => 20000,
            'service_type' => 'regular',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_name' => 'Dokumen Penting',
            'quantity' => 1,
            'weight' => 2.00,
        ]);

        $payment1 = Payment::create([
            'order_id' => $shipment1->tracking_number,
            'shipment_id' => $shipment1->id,
            'amount' => 20000,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
            'snap_token' => 'mock_snap_token_' . $shipment1->tracking_number,
        ]);
        $shipment1->update(['payment_id' => $payment1->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment1->id,
            'location' => 'Jakarta',
            'description' => 'Booking pengiriman dibuat oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(2),
        ]);

        // ===== 2. PAID - WAITING DROPOFF =====
        $shipment2 = Shipment::create([
            'tracking_number' => 'EXP-20260716-EFGH2',
            'booking_code' => 'BK-20260716-EFGH2',
            'customer_id' => $customer->id,
            'branch_id' => $branchJkt?->id,
            'status' => 'waiting_dropoff',
            'origin_city' => 'Jakarta',
            'destination_city' => 'Surabaya',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Citra Penerima',
            'receiver_phone' => '083456789012',
            'receiver_address' => 'Jl. Kertajaya No. 15, Surabaya',
            'estimated_weight' => 3.00,
            'actual_weight' => null,
            'estimated_price' => 60000,
            'actual_price' => null,
            'total_price' => 60000,
            'service_type' => 'regular',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'item_name' => 'Paket Pakaian',
            'quantity' => 1,
            'weight' => 3.00,
        ]);

        $payment2 = Payment::create([
            'order_id' => $shipment2->tracking_number,
            'shipment_id' => $shipment2->id,
            'amount' => 60000,
            'payment_method' => 'midtrans',
            'payment_status' => 'paid',
        ]);
        $shipment2->update(['payment_id' => $payment2->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment2->id,
            'location' => 'Jakarta',
            'description' => 'Booking pengiriman dibuat oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(2)->subHours(2),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment2->id,
            'location' => 'Jakarta',
            'description' => 'Pembayaran lunas via Midtrans. Paket siap dibawa ke outlet.',
            'status' => 'waiting_dropoff',
            'tracked_at' => now()->subDays(2),
        ]);

        // ===== 3. IN TRANSIT =====
        $shipment3 = Shipment::create([
            'tracking_number' => 'EXP-20260715-IJKL3',
            'booking_code' => 'BK-20260715-IJKL3',
            'customer_id' => $customer->id,
            'branch_id' => $branchBdg?->id,
            'courier_id' => $courierBdg?->id,
            'status' => 'in_transit',
            'origin_city' => 'Bandung',
            'destination_city' => 'Medan',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Dedi Penerima',
            'receiver_phone' => '084567890123',
            'receiver_address' => 'Jl. S. Parman No. 20, Medan',
            'estimated_weight' => 5.00,
            'actual_weight' => 5.50,
            'estimated_price' => 190000,
            'actual_price' => 209000,
            'total_price' => 209000,
            'service_type' => 'express',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'item_name' => 'Elektronik (Charger)',
            'quantity' => 2,
            'weight' => 2.50,
        ]);

        $payment3 = Payment::create([
            'order_id' => $shipment3->tracking_number,
            'shipment_id' => $shipment3->id,
            'amount' => 209000,
            'payment_method' => 'midtrans',
            'payment_status' => 'paid',
        ]);
        $shipment3->update(['payment_id' => $payment3->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment3->id,
            'location' => 'Bandung',
            'description' => 'Booking pengiriman dibuat oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(3),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment3->id,
            'location' => 'Bandung',
            'description' => 'Pembayaran lunas via Midtrans.',
            'status' => 'waiting_dropoff',
            'tracked_at' => now()->subDays(3)->addHours(1),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment3->id,
            'location' => 'Bandung',
            'description' => 'Paket diterima di Cabang Bandung Wetan. Berat Aktual: 5.50 kg.',
            'status' => 'received_at_branch',
            'tracked_at' => now()->subDays(3)->addHours(3),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment3->id,
            'location' => 'Bandung',
            'description' => 'Paket diberangkatkan dari Cabang Bandung menuju Medan (transit).',
            'status' => 'in_transit',
            'tracked_at' => now()->subDays(2),
        ]);

        // ===== 4. OUT FOR DELIVERY =====
        $shipment4 = Shipment::create([
            'tracking_number' => 'EXP-20260714-MNOP4',
            'booking_code' => 'BK-20260714-MNOP4',
            'customer_id' => $customer->id,
            'branch_id' => $branchJkt?->id,
            'courier_id' => $courierJkt?->id,
            'status' => 'out_for_delivery',
            'origin_city' => 'Jakarta',
            'destination_city' => 'Jakarta',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Eka Penerima',
            'receiver_phone' => '085678901234',
            'receiver_address' => 'Jl. Kebon Sirih No. 1, Jakarta Pusat',
            'estimated_weight' => 1.00,
            'actual_weight' => 1.20,
            'estimated_price' => 6000,
            'actual_price' => 7200,
            'total_price' => 7200,
            'service_type' => 'express',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment4->id,
            'item_name' => 'Dokumen Surat',
            'quantity' => 1,
            'weight' => 1.00,
        ]);

        $payment4 = Payment::create([
            'order_id' => $shipment4->tracking_number,
            'shipment_id' => $shipment4->id,
            'amount' => 7200,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);
        $shipment4->update(['payment_id' => $payment4->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment4->id,
            'location' => 'Jakarta',
            'description' => 'Booking pengiriman oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(4),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment4->id,
            'location' => 'Jakarta',
            'description' => 'Pembayaran cash diterima di Cabang Jakarta Pusat.',
            'status' => 'received_at_branch',
            'tracked_at' => now()->subDays(4)->addHours(2),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment4->id,
            'location' => 'Jakarta',
            'description' => 'Paket ditugaskan ke Kurir Jakarta untuk pengantaran.',
            'status' => 'assigned_to_courier',
            'tracked_at' => now()->subDays(3),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment4->id,
            'location' => 'Jakarta',
            'description' => 'Paket sedang dalam perjalanan menuju alamat penerima.',
            'status' => 'out_for_delivery',
            'tracked_at' => now()->subHours(3),
        ]);

        // ===== 5. DELIVERED (Completed) =====
        $shipment5 = Shipment::create([
            'tracking_number' => 'EXP-20260710-QRST5',
            'booking_code' => 'BK-20260710-QRST5',
            'customer_id' => $customer->id,
            'branch_id' => $branchJkt?->id,
            'courier_id' => $courierJkt?->id,
            'status' => 'delivered',
            'origin_city' => 'Jakarta',
            'destination_city' => 'Jakarta',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Fani Penerima',
            'receiver_phone' => '086789012345',
            'receiver_address' => 'Jl. MH Thamrin No. 10, Jakarta Pusat',
            'estimated_weight' => 2.00,
            'actual_weight' => 2.00,
            'estimated_price' => 12000,
            'actual_price' => 12000,
            'total_price' => 12000,
            'service_type' => 'regular',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment5->id,
            'item_name' => 'Buku & Alat Tulis',
            'quantity' => 1,
            'weight' => 2.00,
        ]);

        $payment5 = Payment::create([
            'order_id' => $shipment5->tracking_number,
            'shipment_id' => $shipment5->id,
            'amount' => 12000,
            'payment_method' => 'midtrans',
            'payment_status' => 'paid',
        ]);
        $shipment5->update(['payment_id' => $payment5->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment5->id,
            'location' => 'Jakarta',
            'description' => 'Booking pengiriman dibuat oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(7),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment5->id,
            'location' => 'Jakarta',
            'description' => 'Pembayaran lunas via Midtrans.',
            'status' => 'waiting_dropoff',
            'tracked_at' => now()->subDays(7)->addHours(1),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment5->id,
            'location' => 'Jakarta',
            'description' => 'Paket diterima dan ditimbang di Cabang Jakarta Pusat. Lunas.',
            'status' => 'received_at_branch',
            'tracked_at' => now()->subDays(7)->addHours(3),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment5->id,
            'location' => 'Jakarta',
            'description' => 'Paket ditugaskan ke Kurir Jakarta.',
            'status' => 'assigned_to_courier',
            'tracked_at' => now()->subDays(6),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment5->id,
            'location' => 'Jakarta',
            'description' => 'Paket berhasil diterima oleh Fani Penerima. Penerima menandatangani bukti penyerahan.',
            'status' => 'delivered',
            'tracked_at' => now()->subDays(5),
        ]);

        // Delivery Proof for delivered shipment
        DeliveryProof::create([
            'shipment_id' => $shipment5->id,
            'courier_id' => $courierJkt->id,
            'photos' => json_encode(['proofs/sample-delivery-photo.jpg']),
            'notes' => 'Paket diterima dengan baik oleh penerima.',
            'recipient_name' => 'Fani Penerima',
            'recipient_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        // ===== 6. FAILED DELIVERY =====
        $shipment6 = Shipment::create([
            'tracking_number' => 'EXP-20260711-UVWX6',
            'booking_code' => 'BK-20260711-UVWX6',
            'customer_id' => $customer->id,
            'branch_id' => $branchBdg?->id,
            'courier_id' => $courierBdg?->id,
            'status' => 'gagal_kirim',
            'origin_city' => 'Bandung',
            'destination_city' => 'Jakarta',
            'sender_name' => 'Ahmad Customer',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Jenderal Sudirman No. 25, Jakarta',
            'receiver_name' => 'Gita Penerima',
            'receiver_phone' => '087890123456',
            'receiver_address' => 'Jl. Rasuna Said No. 5, Jakarta Selatan',
            'estimated_weight' => 1.50,
            'actual_weight' => 1.50,
            'estimated_price' => 15000,
            'actual_price' => 15000,
            'total_price' => 15000,
            'service_type' => 'regular',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment6->id,
            'item_name' => 'Dokumen Legal',
            'quantity' => 1,
            'weight' => 1.50,
        ]);

        $payment6 = Payment::create([
            'order_id' => $shipment6->tracking_number,
            'shipment_id' => $shipment6->id,
            'amount' => 15000,
            'payment_method' => 'midtrans',
            'payment_status' => 'paid',
        ]);
        $shipment6->update(['payment_id' => $payment6->id]);

        ShipmentTracking::create([
            'shipment_id' => $shipment6->id,
            'location' => 'Bandung',
            'description' => 'Booking pengiriman oleh pengirim.',
            'status' => 'booking_created',
            'tracked_at' => now()->subDays(6),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment6->id,
            'location' => 'Bandung',
            'description' => 'Pembayaran lunas via Midtrans.',
            'status' => 'waiting_dropoff',
            'tracked_at' => now()->subDays(6)->addHour(),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment6->id,
            'location' => 'Bandung',
            'description' => 'Paket ditimbang di Cabang Bandung Wetan. Berat Aktual: 1.50 kg.',
            'status' => 'received_at_branch',
            'tracked_at' => now()->subDays(5),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment6->id,
            'location' => 'Bandung',
            'description' => 'Paket ditugaskan ke Kurir Bandung.',
            'status' => 'assigned_to_courier',
            'tracked_at' => now()->subDays(4),
        ]);
        ShipmentTracking::create([
            'shipment_id' => $shipment6->id,
            'location' => 'Jakarta',
            'description' => 'Pengiriman gagal. Alasan: Alamat penerima tidak ditemukan.',
            'status' => 'gagal_kirim',
            'tracked_at' => now()->subDays(3),
        ]);

        $this->command->info('6 sample transactional records seeded successfully!');
    }
}