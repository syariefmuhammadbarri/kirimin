@extends('layouts.app')

@section('content')
<div class="space-y-16">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto space-y-6 pt-6">
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
            Kirim Paket Lebih Cepat & Hemat <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-400">Langsung dari Rumah Anda</span>
        </h1>
        <p class="text-lg text-slate-400">
            Booking pengiriman online, bayar cashless, dan serahkan ke outlet terdekat dalam waktu kurang dari 3 menit tanpa antre panjang.
        </p>
        <div class="flex justify-center space-x-4 pt-4">
            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-lg transition shadow-lg shadow-blue-900/40">
                Mulai Kirim Sekarang
            </a>
            <a href="{{ route('track.public') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-6 py-3 rounded-lg border border-slate-700 transition">
                Lacak Paket
            </a>
        </div>
    </div>

    <!-- Bento Grid Section -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 max-w-6xl mx-auto">
        <!-- Tracking Card - Large -->
        <a href="{{ route('track.public') }}" class="glass-panel rounded-2xl p-8 shadow-xl border border-slate-800/80 hover:border-blue-500/50 transition group md:col-span-3 md:row-span-2">
            <div class="flex flex-col h-full space-y-4">
                <div class="flex items-center space-x-3">
                    <svg class="h-10 w-10 text-blue-500 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white">Lacak Paket</h3>
                </div>
                <p class="text-slate-400 flex-grow">Cek status pengiriman paket Anda secara real-time dengan nomor resi atau kode booking.</p>
                <div class="flex items-center text-blue-400 font-semibold group-hover:translate-x-1 transition">
                    Lacak Sekarang
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Calculator Card - Medium -->
        <a href="{{ route('calculator') }}" class="glass-panel rounded-2xl p-6 shadow-xl border border-slate-800/80 hover:border-indigo-500/50 transition group md:col-span-3">
            <div class="flex flex-col h-full space-y-3">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-indigo-400 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-white">Kalkulator Ongkir</h3>
                </div>
                <p class="text-sm text-slate-400">Hitung estimasi biaya pengiriman paket berdasarkan kota asal, tujuan, dan berat.</p>
                <div class="flex items-center text-indigo-400 text-sm font-semibold group-hover:translate-x-1 transition">
                    Hitung Sekarang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Branches Card - Medium -->
        <a href="{{ route('branches') }}" class="glass-panel rounded-2xl p-6 shadow-xl border border-slate-800/80 hover:border-emerald-500/50 transition group md:col-span-3">
            <div class="flex flex-col h-full space-y-3">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-emerald-400 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-xl font-bold text-white">Cabang & Outlet</h3>
                </div>
                <p class="text-sm text-slate-400">Temukan lokasi cabang dan outlet BAZMA Express terdekat di kota Anda.</p>
                <div class="flex items-center text-emerald-400 text-sm font-semibold group-hover:translate-x-1 transition">
                    Lihat Cabang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Feature 1 - Small -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl border border-slate-800/80 md:col-span-2">
            <div class="text-3xl mb-3">⚡</div>
            <h3 class="text-lg font-bold text-white mb-2">Cepat & Efisien</h3>
            <p class="text-sm text-slate-400">Proses booking kurang dari 3 menit tanpa antre panjang.</p>
        </div>

        <!-- Feature 2 - Small -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl border border-slate-800/80 md:col-span-2">
            <div class="text-3xl mb-3">🔒</div>
            <h3 class="text-lg font-bold text-white mb-2">Aman & Terpercaya</h3>
            <p class="text-sm text-slate-400">Pembayaran cashless dengan sistem keamanan terenkripsi.</p>
        </div>

        <!-- Feature 3 - Small -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl border border-slate-800/80 md:col-span-2">
            <div class="text-3xl mb-3">📍</div>
            <h3 class="text-lg font-bold text-white mb-2">Jaringan Luas</h3>
            <p class="text-sm text-slate-400">Hadir di kota-kota besar dengan outlet strategis.</p>
        </div>
    </div>
</div>
@endsection