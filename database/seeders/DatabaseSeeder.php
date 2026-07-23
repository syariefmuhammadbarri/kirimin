<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchRoute;
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
        // 0. Seed Cities first — required by showBooking() dropdown
        $this->call(CitiesSeeder::class);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $customerRole = Role::create(['name' => 'customer']);
        $adminRole = Role::create(['name' => 'admin_cabang']);
        $courierRole = Role::create(['name' => 'kurir']);
        $managerRole = Role::create(['name' => 'manager']);
        $ownerRole = Role::create(['name' => 'owner']);

        // 2. Create 8 Hub Branches
        $branchDefs = [
            ['key' => 'Jakarta', 'name' => 'Cabang Jakarta Pusat', 'city' => 'Jakarta', 'address' => 'Jl. Kebon Sirih No. 1, Jakarta Pusat', 'phone' => '021-111111'],
            ['key' => 'Bandung', 'name' => 'Cabang Bandung Wetan', 'city' => 'Bandung', 'address' => 'Jl. Riau No. 10, Bandung Wetan', 'phone' => '022-222222'],
            ['key' => 'Surabaya', 'name' => 'Cabang Surabaya Gubeng', 'city' => 'Surabaya', 'address' => 'Jl. Kertajaya No. 15, Surabaya', 'phone' => '031-333333'],
            ['key' => 'Medan', 'name' => 'Cabang Medan Baru', 'city' => 'Medan', 'address' => 'Jl. S. Parman No. 20, Medan', 'phone' => '061-444444'],
            ['key' => 'Semarang', 'name' => 'Cabang Semarang Hub', 'city' => 'Semarang', 'address' => 'Jl. Pemuda No. 88, Semarang', 'phone' => '024-555555'],
            ['key' => 'Yogyakarta', 'name' => 'Cabang Yogyakarta Hub', 'city' => 'Yogyakarta', 'address' => 'Jl. Malioboro No. 45, Yogyakarta', 'phone' => '0274-666666'],
            ['key' => 'Makassar', 'name' => 'Cabang Makassar Hub', 'city' => 'Makassar', 'address' => 'Jl. Ujung Pandang No. 12, Makassar', 'phone' => '0411-777777'],
            ['key' => 'Palembang', 'name' => 'Cabang Palembang Hub', 'city' => 'Palembang', 'address' => 'Jl. Sudirman No. 100, Palembang', 'phone' => '0711-888888'],
        ];

        $branches = [];
        foreach ($branchDefs as $b) {
            $branches[$b['key']] = Branch::create([
                'name' => $b['name'],
                'city' => $b['city'],
                'address' => $b['address'],
                'phone' => $b['phone'],
            ]);
        }

        $branchJkt = $branches['Jakarta'];
        $branchBdg = $branches['Bandung'];

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

        // Create Admin & 2-3 Couriers per Branch
        $couriersMap = [];
        foreach ($branches as $city => $branch) {
            $slug = strtolower($city);

            // Admin Branch
            $adminUser = User::create([
                'name' => "Admin {$city}",
                'email' => "admin.{$slug}@ekspedisi.com",
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'email_verified_at' => now(),
            ]);
            $adminUser->assignRole($adminRole);

            // Courier 1 (Keep legacy email format for Jakarta & Bandung)
            $c1Email = match($city) {
                'Jakarta' => 'courier.jakarta@ekspedisi.com',
                'Bandung' => 'courier.bandung@ekspedisi.com',
                default => "courier.{$slug}.1@ekspedisi.com",
            };

            $c1 = User::create([
                'name' => "Kurir {$city} 1",
                'email' => $c1Email,
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'email_verified_at' => now(),
            ]);
            $c1->assignRole($courierRole);

            // Courier 2
            $c2 = User::create([
                'name' => "Kurir {$city} 2",
                'email' => "courier.{$slug}.2@ekspedisi.com",
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'email_verified_at' => now(),
            ]);
            $c2->assignRole($courierRole);

            $couriersMap[$city] = [$c1, $c2];
        }

        $courierJkt = $couriersMap['Jakarta'][0];
        $courierBdg = $couriersMap['Bandung'][0];

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
            'phone' => '081234567890',
            'address' => 'Jl. Jenderal Sudirman No. 25',
            'city' => 'Jakarta',
        ]);

        // 4. Create Inter-Branch Route Graph (8 Hub Nodes, 9 Edges)
        $routesData = [
            ['Jakarta', 'Bandung', 150],
            ['Jakarta', 'Semarang', 450],
            ['Semarang', 'Yogyakarta', 65],
            ['Semarang', 'Surabaya', 310],
            ['Yogyakarta', 'Surabaya', 290],
            ['Surabaya', 'Makassar', 860],
            ['Jakarta', 'Palembang', 470],
            ['Palembang', 'Medan', 1400],
            ['Bandung', 'Yogyakarta', 315],
        ];

        foreach ($routesData as $r) {
            $fromB = $branches[$r[0]] ?? null;
            $toB = $branches[$r[1]] ?? null;
            if ($fromB && $toB) {
                BranchRoute::create([
                    'from_branch_id' => $fromB->id,
                    'to_branch_id' => $toB->id,
                    'distance_km' => $r[2],
                    'is_active' => true,
                ]);
            }
        }

        // 5. Create Rates Matrix for Hub Cities
        $cityList = array_keys($branches);
        foreach ($cityList as $orig) {
            foreach ($cityList as $dest) {
                if ($orig === $dest) {
                    $price = 6000;
                    $days = 1;
                } else {
                    $price = 15000;
                    $days = 3;
                }
                Rate::create([
                    'origin_city' => $orig,
                    'destination_city' => $dest,
                    'price_per_kg' => $price,
                    'estimated_days' => $days,
                ]);
            }
        }

        // 6. Create Settings
        Setting::setValue('company_name', 'BAZMA Express', 'Nama perusahaan ekspedisi');
        Setting::setValue('company_address', 'Jl. Raya BAZMA No. 1, Bogor, Jawa Barat', 'Alamat kantor pusat');
        Setting::setValue('company_phone', '0812-3456-7890', 'Nomor HP/WA CS');
        Setting::setValue('company_email', 'support@bazma-express.com', 'Alamat email bantuan');
        Setting::setValue('midtrans_mock_mode', 'true', 'Gunakan simulasi checkout lokal jika serverKey kosong/tidak valid');
        Setting::setValue('booking_expiry_hours', '24', 'Batas waktu pembayaran booking dalam jam (default: 24 jam)');
        Setting::setValue('max_delivery_attempts', '3', 'Jumlah maksimal percobaan pengantaran sebelum paket dikembalikan (return-to-sender)');

        // 7. Create Vehicles
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

        // 8. Seed Transactional Sample Data
        $this->call(ShipmentTransactionSeeder::class);

        // 9. Seed Landing Contents
        LandingContent::create([
            'section' => 'hero',
            'title' => 'Kirim Paket Lebih Cepat & Hemat',
            'content' => 'Booking pengiriman online, bayar cashless, dan serahkan ke outlet terdekat dalam waktu kurang dari 3 menit tanpa antre panjang.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        LandingContent::create([
            'section' => 'promo',
            'title' => 'Free Ongkir',
            'content' => 'Gratis ongkos kirim untuk pelanggan baru pertama kali booking. Syarat & ketentuan berlaku.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        LandingContent::create([
            'section' => 'features',
            'title' => 'Lacak Paket Real-time',
            'content' => 'Pantau posisi paket Anda secara real-time dari cabang asal hingga diterima penerima.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        LandingContent::create([
            'section' => 'about',
            'title' => 'Tentang Kirimin Express',
            'content' => 'Kirimin Express adalah layanan ekspedisi terpercaya yang melayani pengiriman ke seluruh Indonesia. Dengan teknologi modern dan jaringan 8 cabang hub logistik, kami memastikan setiap paket sampai dengan aman, tepat waktu, dan dengan harga terjangkau.',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        LandingContent::create([
            'section'   => 'contact',
            'title'     => 'Hubungi Kami',
            'content'   => 'Ada pertanyaan? Tim CS kami siap membantu 24/7 melalui WhatsApp, email, atau kunjungi cabang terdekat.',
            'image'     => null,
            'order'     => 1,
            'is_active' => true,
        ]);
    }
}
