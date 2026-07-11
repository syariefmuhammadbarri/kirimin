@extends('layouts.app')

@section('content')
<div class="space-y-24">
    <!-- Hero Section -->
    <div class="text-center max-w-4xl mx-auto space-y-8 pt-16">
        <div class="reveal-text">
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-slate-900 leading-[1.1]">
                Kirim Paket Lebih Cepat & Hemat <br>
                <span class="hero-gradient text-transparent bg-clip-text">Langsung dari Rumah Anda</span>
            </h1>
        </div>
        <p class="reveal-text reveal-text-delay-1 text-lg sm:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Booking pengiriman online, bayar cashless, dan serahkan ke outlet terdekat dalam waktu kurang dari 3 menit tanpa antre panjang.
        </p>
        <div class="reveal-text reveal-text-delay-2 flex justify-center space-x-4 pt-6">
            <a href="{{ route('register') }}" class="magnetic-btn bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3.5 rounded-xl transition shadow-sm">
                Mulai Kirim Sekarang
            </a>
            <a href="{{ route('track.public') }}" class="magnetic-btn bg-white hover:bg-slate-50 text-slate-700 font-semibold px-8 py-3.5 rounded-xl border border-slate-200 transition shadow-sm">
                Lacak Paket
            </a>
        </div>
    </div>

    <!-- Subtle Divider -->
    <hr class="section-divider max-w-6xl mx-auto">

    <!-- Bento Grid Section -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-5 max-w-6xl mx-auto">
        <!-- Tracking Card - Large -->
        <a href="{{ route('track.public') }}" class="spotlight-card card-panel rounded-2xl p-8 shadow-sm border border-black/5 hover:border-blue-400/30 hover:shadow-md transition group md:col-span-3 md:row-span-2 reveal-card">
            <div class="flex flex-col h-full space-y-5">
                <div class="flex items-center space-x-3">
                    <svg class="h-10 w-10 text-blue-600 group-hover:scale-110 transition float-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-slate-900">Lacak Paket</h3>
                </div>
                <p class="text-slate-500 flex-grow leading-relaxed">Cek status pengiriman paket Anda secara real-time dengan nomor resi atau kode booking.</p>
                <div class="flex items-center text-blue-600 font-semibold group-hover:translate-x-2 transition">
                    Lacak Sekarang
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Calculator Card - Medium -->
        <a href="{{ route('calculator') }}" class="spotlight-card card-panel rounded-2xl p-7 shadow-sm border border-black/5 hover:border-indigo-400/30 hover:shadow-md transition group md:col-span-3 reveal-card reveal-text-delay-1">
            <div class="flex flex-col h-full space-y-4">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-indigo-600 group-hover:scale-110 transition float-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-slate-900">Kalkulator Ongkir</h3>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-grow">Hitung estimasi biaya pengiriman paket berdasarkan kota asal, tujuan, dan berat.</p>
                <div class="flex items-center text-indigo-600 text-sm font-semibold group-hover:translate-x-2 transition">
                    Hitung Sekarang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Branches Card - Medium -->
        <a href="{{ route('branches') }}" class="spotlight-card card-panel rounded-2xl p-7 shadow-sm border border-black/5 hover:border-emerald-400/30 hover:shadow-md transition group md:col-span-3 reveal-card reveal-text-delay-2">
            <div class="flex flex-col h-full space-y-4">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-emerald-600 group-hover:scale-110 transition float-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-xl font-bold text-slate-900">Cabang & Outlet</h3>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-grow">Temukan lokasi cabang dan outlet BAZMA Express terdekat di kota Anda.</p>
                <div class="flex items-center text-emerald-600 text-sm font-semibold group-hover:translate-x-2 transition">
                    Lihat Cabang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Feature 1 - Small -->
        <div class="spotlight-card card-panel rounded-2xl p-6 shadow-sm border border-black/5 hover:border-black/10 hover:shadow-md transition md:col-span-2 reveal-card reveal-text-delay-3">
            <div class="text-3xl mb-3 float-slow">⚡</div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Cepat & Efisien</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Proses booking kurang dari 3 menit tanpa antre panjang.</p>
        </div>

        <!-- Feature 2 - Small -->
        <div class="spotlight-card card-panel rounded-2xl p-6 shadow-sm border border-black/5 hover:border-black/10 hover:shadow-md transition md:col-span-2 reveal-card reveal-text-delay-4">
            <div class="text-3xl mb-3 float-slow">🔒</div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Aman & Terpercaya</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Pembayaran cashless dengan sistem keamanan terenkripsi.</p>
        </div>

        <!-- Feature 3 - Small -->
        <div class="spotlight-card card-panel rounded-2xl p-6 shadow-sm border border-black/5 hover:border-black/10 hover:shadow-md transition md:col-span-2 reveal-card reveal-text-delay-5">
            <div class="text-3xl mb-3 float-slow">📍</div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Jaringan Luas</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Hadir di kota-kota besar dengan outlet strategis.</p>
        </div>
    </div>
</div>
@endsection