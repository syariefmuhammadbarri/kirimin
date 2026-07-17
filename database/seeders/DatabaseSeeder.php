<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\LandingContent;
use App\Models\Rate;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $customerRole = Role::create(['name' => 'customer']);
        $adminRole = Role::create(['name' => 'admin_cabang']);
        $courierRole = Role::create(['name' => 'kurir']);
        $managerRole = Role::create(['name' => 'manager']);
        $ownerRole = Role::create(['name' => 'owner']);

        // 2. Create Branches
        $branchJkt = Branch::create([
            'name' => 'Cabang Jakarta Pusat',
            'city' => 'Jakarta',
            'address' => 'Jl. Kebon Sirih No. 1, Jakarta Pusat',
            'phone' => '021-111111'
        ]);

        $branchBdg = Branch::create([
            'name' => 'Cabang Bandung Wetan',
            'city' => 'Bandung',
            'address' => 'Jl. Riau No. 10, Bandung Wetan',
            'phone' => '022-222222'
        ]);

        $branchSby = Branch::create([
            'name' => 'Cabang Surabaya Gubeng',
            'city' => 'Surabaya',
            'address' => 'Jl. Kertajaya No. 15, Surabaya',
            'phone' => '031-333333'
        ]);

        $branchMdn = Branch::create([
            'name' => 'Cabang Medan Baru',
            'city' => 'Medan',
            'address' => 'Jl. S. Parman No. 20, Medan',
            'phone' => '061-444444'
        ]);

        // 3. Create Users & Assign Roles

        // Manager / Super Admin
        $manager = User::create([
            'name' => 'Manager BAZMA',
            'email' => 'manager@ekspedisi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole($managerRole);

        // Owner
        $owner = User::create([
            'name' => 'Owner BAZMA',
            'email' => 'owner@ekspedisi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $owner->assignRole($ownerRole);

        // Admin Jakarta
        $adminJkt = User::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jakarta@ekspedisi.com',
            'password' => Hash::make('password'),
            'branch_id' => $branchJkt->id,
            'email_verified_at' => now(),
        ]);
        $adminJkt->assignRole($adminRole);

        // Admin Bandung
        $adminBdg = User::create([
            'name' => 'Admin Bandung',
            'email' => 'admin.bandung@ekspedisi.com',
            'password' => Hash::make('password'),
            'branch_id' => $branchBdg->id,
            'email_verified_at' => now(),
        ]);
        $adminBdg->assignRole($adminRole);

        // Courier Jakarta
        $courierJkt = User::create([
            'name' => 'Kurir Jakarta',
            'email' => 'courier.jakarta@ekspedisi.com',
            'password' => Hash::make('password'),
            'branch_id' => $branchJkt->id,
            'email_verified_at' => now(),
        ]);
        $courierJkt->assignRole($courierRole);

        // Courier Bandung
        $courierBdg = User::create([
            'name' => 'Kurir Bandung',
            'email' => 'courier.bandung@ekspedisi.com',
            'password' => Hash::make('password'),
            'branch_id' => $branchBdg->id,
            'email_verified_at' => now(),
        ]);
        $courierBdg->assignRole($courierRole);

        // Customer User & Profile
        $customerUser = User::create([
            'name' => 'Ahmad Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $customerUser->assignRole($customerRole);

        Customer::create([
            'user_id' => $customerUser->id,
            'name' => $customerUser->name,
            'email' => $customerUser->email,
            'phone' => '081234567890',
            'address' => 'Jl. Jenderal Sudirman No. 25',
            'city' => 'Jakarta',
            'email_verified_at' => now()
        ]);

        // 4. Create Rates
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan'];
        $ratesMatrix = [
            'Jakarta' => [
                'Bandung' => [10000, 2],
                'Surabaya' => [20000, 3],
                'Medan' => [35000, 4],
                'Jakarta' => [6000, 1]
            ],
            'Bandung' => [
                'Jakarta' => [10000, 2],
                'Surabaya' => [22000, 3],
                'Medan' => [38000, 4],
                'Bandung' => [6000, 1]
            ],
            'Surabaya' => [
                'Jakarta' => [20000, 3],
                'Bandung' => [22000, 3],
                'Medan' => [45000, 5],
                'Surabaya' => [6000, 1]
            ],
            'Medan' => [
                'Jakarta' => [35000, 4],
                'Bandung' => [38000, 4],
                'Surabaya' => [45000, 5],
                'Medan' => [6000, 1]
            ]
        ];

        foreach ($ratesMatrix as $origin => $destinations) {
            foreach ($destinations as $dest => $data) {
                Rate::create([
                    'origin_city' => $origin,
                    'destination_city' => $dest,
                    'price_per_kg' => $data[0],
                    'estimated_days' => $data[1]
                ]);
            }
        }

        // 5. Create Settings
        Setting::setValue('company_name', 'BAZMA Express', 'Nama perusahaan ekspedisi');
        Setting::setValue('company_address', 'Jl. Raya BAZMA No. 1, Bogor, Jawa Barat', 'Alamat kantor pusat');
        Setting::setValue('company_phone', '0812-3456-7890', 'Nomor HP/WA CS');
        Setting::setValue('company_email', 'support@bazma-express.com', 'Alamat email bantuan');
        Setting::setValue('midtrans_mock_mode', 'true', 'Gunakan simulasi checkout lokal jika serverKey kosong/tidak valid');

        // 6. Create Vehicles
        Vehicle::create([
            'plate_number' => 'B 1234 BZM',
            'type' => 'motor',
            'courier_id' => $courierJkt->id
        ]);

        Vehicle::create([
            'plate_number' => 'D 5678 BZM',
            'type' => 'motor',
            'courier_id' => $courierBdg->id
        ]);

        Vehicle::create([
            'plate_number' => 'B 9999 TRK',
            'type' => 'truck',
            'courier_id' => null
        ]);
        // 7. Seed Landing Contents
        // Hero Section
        LandingContent::create([
            'section' => 'hero',
            'title' => 'Kirim Paket Lebih Cepat & Hemat',
            'content' => 'Booking pengiriman online, bayar cashless, dan serahkan ke outlet terdekat dalam waktu kurang dari 3 menit tanpa antre panjang.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        // Promo Section items
        LandingContent::create([
            'section' => 'promo',
            'title' => 'Free Ongkir',
            'content' => 'Gratis ongkos kirim untuk pelanggan baru pertama kali booking. Syarat & ketentuan berlaku.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);
        LandingContent::create([
            'section' => 'promo',
            'title' => 'Diskon 20%',
            'content' => 'Hemat 20% untuk pengiriman reguler ke seluruh kota setiap hari Selasa dan Rabu.',
            'image' => null,
            'order' => 2,
            'is_active' => true,
        ]);
        LandingContent::create([
            'section' => 'promo',
            'title' => 'Flash Delivery',
            'content' => 'Layanan ekspres same-day untuk pengiriman dalam kota. Jaminan tiba hari ini.',
            'image' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        // Features Section items
        LandingContent::create([
            'section' => 'features',
            'title' => 'Lacak Paket Real-time',
            'content' => 'Pantau posisi paket Anda secara real-time dari cabang asal hingga diterima penerima.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);
        LandingContent::create([
            'section' => 'features',
            'title' => 'Kalkulator Ongkir',
            'content' => 'Hitung estimasi biaya pengiriman secara instan sebelum booking. Tanpa biaya tersembunyi.',
            'image' => null,
            'order' => 2,
            'is_active' => true,
        ]);
        LandingContent::create([
            'section' => 'features',
            'title' => 'Jaringan Luas',
            'content' => 'Didukung cabang di berbagai kota besar Indonesia. Cepat, aman, dan terpercaya.',
            'image' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        // About Section
        LandingContent::create([
            'section' => 'about',
            'title' => 'Tentang Kirimin Express',
            'content' => 'Kirimin Express adalah layanan ekspedisi terpercaya yang melayani pengiriman ke seluruh Indonesia. Dengan teknologi modern dan jaringan cabang yang luas, kami memastikan setiap paket sampai dengan aman, tepat waktu, dan dengan harga terjangkau.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        // Contact Section
        LandingContent::create([
            'section' => 'contact',
            'title' => 'Hubungi Kami',
            'content' => 'Ada pertanyaan? Tim CS kami siap membantu 24/7 melalui WhatsApp, email, atau kunjungi cabang terdekat.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);
    }
}
