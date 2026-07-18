<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Banten
            ['name' => 'KOTA TANGERANG', 'province' => 'Banten', 'type' => 'Kota'],
            ['name' => 'KOTA TANGERANG SELATAN', 'province' => 'Banten', 'type' => 'Kota'],
            ['name' => 'KOTA SERANG', 'province' => 'Banten', 'type' => 'Kota'],
            ['name' => 'KOTA CILEGON', 'province' => 'Banten', 'type' => 'Kota'],
            ['name' => 'KABUPATEN TANGERANG', 'province' => 'Banten', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN SERANG', 'province' => 'Banten', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN LEBAK', 'province' => 'Banten', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN PANDEGLANG', 'province' => 'Banten', 'type' => 'Kabupaten'],

            // DKI Jakarta
            ['name' => 'KOTA JAKARTA PUSAT', 'province' => 'DKI Jakarta', 'type' => 'Kota'],
            ['name' => 'KOTA JAKARTA BARAT', 'province' => 'DKI Jakarta', 'type' => 'Kota'],
            ['name' => 'KOTA JAKARTA UTARA', 'province' => 'DKI Jakarta', 'type' => 'Kota'],
            ['name' => 'KOTA JAKARTA SELATAN', 'province' => 'DKI Jakarta', 'type' => 'Kota'],
            ['name' => 'KOTA JAKARTA TIMUR', 'province' => 'DKI Jakarta', 'type' => 'Kota'],
            ['name' => 'KABUPATEN KEPULAUAN SERIBU', 'province' => 'DKI Jakarta', 'type' => 'Kabupaten'],

            // Jawa Barat
            ['name' => 'KOTA BANDUNG', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA BEKASI', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA BOGOR', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA DEPOK', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA CIMAHI', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA CIREBON', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA SUKABUMI', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA TASIKMALAYA', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KOTA BANJAR', 'province' => 'Jawa Barat', 'type' => 'Kota'],
            ['name' => 'KABUPATEN BOGOR', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN SUKABUMI', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN CIANJUR', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BANDUNG', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN GARUT', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN TASIKMALAYA', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN CIAMIS', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN KUNINGAN', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN CIREBON', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN MAJALENGKA', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN SUMEDANG', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN INDRAMAYU', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN SUBANG', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN PURWAKARTA', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN KARAWANG', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BEKASI', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN PANGANDARAN', 'province' => 'Jawa Barat', 'type' => 'Kabupaten'],

            // Jawa Tengah
            ['name' => 'KOTA SEMARANG', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA SURAKARTA', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA TEGAL', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA SALATIGA', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA PEKALONGAN', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA MAGELANG', 'province' => 'Jawa Tengah', 'type' => 'Kota'],
            ['name' => 'KABUPATEN CILACAP', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BANYUMAS', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BREBES', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN KUDUS', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN JEPARA', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN PATI', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN KLATEN', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BOYOLALI', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten'],

            // DI Yogyakarta
            ['name' => 'KOTA YOGYAKARTA', 'province' => 'DI Yogyakarta', 'type' => 'Kota'],
            ['name' => 'KABUPATEN SLEMAN', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BANTUL', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN KULON PROGO', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN GUNUNGKIDUL', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten'],

            // Jawa Timur
            ['name' => 'KOTA SURABAYA', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA MALANG', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA KEDIRI', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA MADIUN', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA MOJOKERTO', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA PASURUAN', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA PROBOLINGGO', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA BLITAR', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KOTA BATU', 'province' => 'Jawa Timur', 'type' => 'Kota'],
            ['name' => 'KABUPATEN SIDOARJO', 'province' => 'Jawa Timur', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN GRESIK', 'province' => 'Jawa Timur', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN BANYUWANGI', 'province' => 'Jawa Timur', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN JEMBER', 'province' => 'Jawa Timur', 'type' => 'Kabupaten'],

            // Bali, NTB, NTT
            ['name' => 'KOTA DENPASAR', 'province' => 'Bali', 'type' => 'Kota'],
            ['name' => 'KABUPATEN BADUNG', 'province' => 'Bali', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN GIANYAR', 'province' => 'Bali', 'type' => 'Kabupaten'],
            ['name' => 'KOTA MATARAM', 'province' => 'Nusa Tenggara Barat', 'type' => 'Kota'],
            ['name' => 'KOTA KUPANG', 'province' => 'Nusa Tenggara Timur', 'type' => 'Kota'],

            // Sumatera Utara
            ['name' => 'KOTA MEDAN', 'province' => 'Sumatera Utara', 'type' => 'Kota'],
            ['name' => 'KOTA BINJAI', 'province' => 'Sumatera Utara', 'type' => 'Kota'],
            ['name' => 'KOTA PEMATANGSIANTAR', 'province' => 'Sumatera Utara', 'type' => 'Kota'],
            ['name' => 'KABUPATEN DELI SERDANG', 'province' => 'Sumatera Utara', 'type' => 'Kabupaten'],

            // Sumatera Barat & Riau
            ['name' => 'KOTA PADANG', 'province' => 'Sumatera Barat', 'type' => 'Kota'],
            ['name' => 'KOTA BUKITTINGGI', 'province' => 'Sumatera Barat', 'type' => 'Kota'],
            ['name' => 'KOTA PEKANBARU', 'province' => 'Riau', 'type' => 'Kota'],
            ['name' => 'KOTA DUMAI', 'province' => 'Riau', 'type' => 'Kota'],
            ['name' => 'KOTA BATAM', 'province' => 'Kepulauan Riau', 'type' => 'Kota'],
            ['name' => 'KOTA TANJUNGPINANG', 'province' => 'Kepulauan Riau', 'type' => 'Kota'],

            // Sumatera Selatan, Lampung, Jambi
            ['name' => 'KOTA PALEMBANG', 'province' => 'Sumatera Selatan', 'type' => 'Kota'],
            ['name' => 'KOTA BANDAR LAMPUNG', 'province' => 'Lampung', 'type' => 'Kota'],
            ['name' => 'KOTA METRO', 'province' => 'Lampung', 'type' => 'Kota'],
            ['name' => 'KOTA JAMBI', 'province' => 'Jambi', 'type' => 'Kota'],
            ['name' => 'KOTA BENGKULU', 'province' => 'Bengkulu', 'type' => 'Kota'],
            ['name' => 'KOTA PANGKAL PINANG', 'province' => 'Bangka Belitung', 'type' => 'Kota'],

            // Kalimantan
            ['name' => 'KOTA PONTIANAK', 'province' => 'Kalimantan Barat', 'type' => 'Kota'],
            ['name' => 'KOTA PALANGKARAYA', 'province' => 'Kalimantan Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA BANJARMASIN', 'province' => 'Kalimantan Selatan', 'type' => 'Kota'],
            ['name' => 'KOTA SAMARINDA', 'province' => 'Kalimantan Timur', 'type' => 'Kota'],
            ['name' => 'KOTA BALIKPAPAN', 'province' => 'Kalimantan Timur', 'type' => 'Kota'],
            ['name' => 'KOTA TARAKAN', 'province' => 'Kalimantan Utara', 'type' => 'Kota'],

            // Sulawesi
            ['name' => 'KOTA MAKASSAR', 'province' => 'Sulawesi Selatan', 'type' => 'Kota'],
            ['name' => 'KOTA MANADO', 'province' => 'Sulawesi Utara', 'type' => 'Kota'],
            ['name' => 'KOTA PALU', 'province' => 'Sulawesi Tengah', 'type' => 'Kota'],
            ['name' => 'KOTA KENDARI', 'province' => 'Sulawesi Tenggara', 'type' => 'Kota'],
            ['name' => 'KOTA GORONTALO', 'province' => 'Gorontalo', 'type' => 'Kota'],
            ['name' => 'KOTA MAMUJU', 'province' => 'Sulawesi Barat', 'type' => 'Kota'],

            // Maluku & Papua
            ['name' => 'KOTA AMBON', 'province' => 'Maluku', 'type' => 'Kota'],
            ['name' => 'KOTA TERNATE', 'province' => 'Maluku Utara', 'type' => 'Kota'],
            ['name' => 'KOTA JAYAPURA', 'province' => 'Papua', 'type' => 'Kota'],
            ['name' => 'KOTA SORONG', 'province' => 'Papua Barat', 'type' => 'Kota'],
            ['name' => 'KABUPATEN MIMIKA', 'province' => 'Papua Tengah', 'type' => 'Kabupaten'],
            ['name' => 'KABUPATEN MERAUKE', 'province' => 'Papua Selatan', 'type' => 'Kabupaten'],
        ];

        foreach ($cities as $city) {
            \App\Models\City::create($city);
        }
    }
}
