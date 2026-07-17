@extends('layouts.app')

@section('content')
<div class="space-y-28 sm:space-y-36">
    <!-- ===== HERO SECTION ===== -->
    <section class="relative pt-16 sm:pt-24 pb-4">
        <!-- Subtle background glow -->
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-48 -right-48 w-[500px] h-[500px] rounded-full bg-blue-50/60 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] rounded-full bg-blue-50/40 blur-3xl"></div>
        </div>

        <div class="text-center max-w-4xl mx-auto">
            <!-- Badge -->
            <div class="reveal-text inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-sm font-medium mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Layanan Pengiriman Terpercaya
            </div>

@php
    $heroContent = $landingContents['hero'][0] ?? null;
@endphp
            <!-- Heading -->
            <div class="reveal-text reveal-text-delay-1">
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-gray-900 leading-[1.05]">
                    {{ $heroContent ? $heroContent->title : 'Kirim Paket Lebih' }}<br>
                    @if(!$heroContent)<span class="hero-gradient">Cepat & Hemat</span>@endif
                </h1>
            </div>

            <!-- Subtext -->
            <p class="reveal-text reveal-text-delay-2 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mt-6 leading-relaxed">
                {{ $heroContent ? $heroContent->content : 'Booking pengiriman online, bayar cashless, dan serahkan ke outlet terdekat dalam waktu kurang dari 3 menit tanpa antre panjang.' }}
            </p>

            <!-- CTA Buttons -->
            <div class="reveal-text reveal-text-delay-3 flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4 mt-10">
                <a href="{{ route('register') }}" class="magnetic-btn inline-flex items-center bg-gray-900 hover:bg-gray-800 text-white font-semibold px-8 py-3.5 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-[0.98]">
                    Mulai Kirim Sekarang
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('track.public') }}" class="magnetic-btn inline-flex items-center bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-3.5 rounded-xl border border-gray-200 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">
                    Lacak Paket
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Stats -->
            <div class="reveal-text reveal-text-delay-4 mt-16 pt-8 border-t border-gray-100">
                <div class="grid grid-cols-3 gap-8 max-w-lg mx-auto">
                    <div>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">15K+</p>
                        <p class="text-sm text-gray-500 mt-1">Paket Terkirim</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">50+</p>
                        <p class="text-sm text-gray-500 mt-1">Kota Tujuan</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">98%</p>
                        <p class="text-sm text-gray-500 mt-1">Tepat Waktu</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PROMO / CAMPAIGN SECTION ===== -->
    <section class="max-w-6xl mx-auto">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">Promo</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-3">Promo & Campaign</h2>
                <p class="text-sm text-gray-500 mt-1.5">Nikmati berbagai penawaran spesial dari kami</p>
            </div>
            <div class="hidden sm:flex space-x-2">
                <button id="promo-prev" class="p-2.5 rounded-full border border-gray-200 hover:bg-gray-100 transition text-gray-600 hover:text-gray-900" aria-label="Previous">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="promo-next" class="p-2.5 rounded-full border border-gray-200 hover:bg-gray-100 transition text-gray-600 hover:text-gray-900" aria-label="Next">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

@php
    $promoItems = $landingContents['promo'] ?? null;
    $promoIcons = [
        '<svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
        '<svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>',
        '<svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        '<svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    ];
@endphp
        <div id="promo-carousel" class="promo-carousel flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0">
            @if($promoItems && $promoItems->count())
                @foreach($promoItems as $i => $promo)
                <div class="promo-card card-panel rounded-2xl p-6 snap-start reveal-card {{ $i > 0 ? 'reveal-text-delay-'.$i : '' }} min-w-[280px] sm:min-w-[300px]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            {!! $promoIcons[$i % count($promoIcons)] !!}
                        </div>
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Promo</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $promo->title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $promo->content }}</p>
                </div>
                @endforeach
            @else
            <!-- Free Ongkir -->
            <div class="promo-card card-panel rounded-2xl p-6 snap-start reveal-card min-w-[280px] sm:min-w-[300px]">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Promo</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Free Ongkir</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Gratis biaya kirim untuk semua tujuan dengan minimal belanja Rp50.000.</p>
            </div>
            <!-- Diskon 20% -->
            <div class="promo-card card-panel rounded-2xl p-6 snap-start reveal-card reveal-text-delay-1 min-w-[280px] sm:min-w-[300px]">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Diskon</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Diskon 20%</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Nikmati diskon 20% untuk semua layanan pengiriman hari ini.</p>
            </div>
            <!-- Flash Delivery -->
            <div class="promo-card card-panel rounded-2xl p-6 snap-start reveal-card reveal-text-delay-2 min-w-[280px] sm:min-w-[300px]">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Kilat</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Flash Delivery</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Layanan kilat 1-2 jam sampai untuk area Jabodetabek.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- ===== DIVIDER ===== -->
    <hr class="section-divider max-w-6xl mx-auto">

    <!-- ===== FEATURES BENTO GRID ===== -->
    <section class="grid grid-cols-1 md:grid-cols-6 gap-5 max-w-6xl mx-auto">
        <!-- Tracking Card - Large -->
        <a href="{{ route('track.public') }}" class="spotlight-card card-panel rounded-2xl p-8 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 group md:col-span-3 md:row-span-2 reveal-card">
            <div class="flex flex-col h-full space-y-5">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Lacak Paket</h3>
                </div>
                <p class="text-gray-600 flex-grow leading-relaxed">Cek status pengiriman paket Anda secara real-time dengan nomor resi atau kode booking.</p>
                <div class="flex items-center text-blue-600 font-semibold group-hover:translate-x-2 transition">
                    Lacak Sekarang
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>

        <!-- Calculator Card - Medium -->
        <a href="{{ route('calculator') }}" class="spotlight-card card-panel rounded-2xl p-7 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 group md:col-span-3 reveal-card reveal-text-delay-1">
            <div class="flex flex-col h-full space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Kalkulator Ongkir</h3>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed flex-grow">Hitung estimasi biaya pengiriman paket berdasarkan kota asal, tujuan, dan berat.</p>
                <div class="flex items-center text-blue-600 text-sm font-semibold group-hover:translate-x-2 transition">
                    Hitung Sekarang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>

        <!-- Branches Card - Medium -->
        <a href="{{ route('branches') }}" class="spotlight-card card-panel rounded-2xl p-7 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 group md:col-span-3 reveal-card reveal-text-delay-2">
            <div class="flex flex-col h-full space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Cabang & Outlet</h3>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed flex-grow">Temukan lokasi cabang dan outlet terdekat di kota Anda.</p>
                <div class="flex items-center text-blue-600 text-sm font-semibold group-hover:translate-x-2 transition">
                    Lihat Cabang
                    <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>

        <!-- Feature 1 -->
        <div class="spotlight-card card-panel rounded-2xl p-6 border border-gray-200 hover:border-blue-200 hover:shadow-md transition-all duration-300 md:col-span-2 reveal-card reveal-text-delay-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
                <span class="text-lg">⚡</span>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Cepat & Efisien</h3>
            <p class="text-sm text-gray-600 leading-relaxed">Proses booking kurang dari 3 menit tanpa antre panjang.</p>
        </div>

        <!-- Feature 2 -->
        <div class="spotlight-card card-panel rounded-2xl p-6 border border-gray-200 hover:border-blue-200 hover:shadow-md transition-all duration-300 md:col-span-2 reveal-card reveal-text-delay-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
                <span class="text-lg">🔒</span>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Aman & Terpercaya</h3>
            <p class="text-sm text-gray-600 leading-relaxed">Pembayaran cashless dengan sistem keamanan terenkripsi.</p>
        </div>

        <!-- Feature 3 -->
        <div class="spotlight-card card-panel rounded-2xl p-6 border border-gray-200 hover:border-blue-200 hover:shadow-md transition-all duration-300 md:col-span-2 reveal-card reveal-text-delay-5">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
                <span class="text-lg">📍</span>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Jaringan Luas</h3>
            <p class="text-sm text-gray-600 leading-relaxed">Hadir di kota-kota besar dengan outlet strategis.</p>
        </div>
    </section>

    {{-- ===== DYNAMIC CMS SECTIONS (about, contact, etc.) ===== --}}
    @if(isset($landingContents['about']))
    <section class="max-w-6xl mx-auto">
        <hr class="section-divider mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="reveal-text">
                <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full mb-4">Tentang Kami</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ $landingContents['about'][0]->title }}</h2>
                <p class="text-gray-600 leading-relaxed">{{ $landingContents['about'][0]->content }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 reveal-card">
                <div class="card-panel rounded-2xl p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600 mb-1">15K+</p>
                    <p class="text-sm text-gray-500">Paket Terkirim</p>
                </div>
                <div class="card-panel rounded-2xl p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600 mb-1">4+</p>
                    <p class="text-sm text-gray-500">Kota Cabang</p>
                </div>
                <div class="card-panel rounded-2xl p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600 mb-1">98%</p>
                    <p class="text-sm text-gray-500">Tepat Waktu</p>
                </div>
                <div class="card-panel rounded-2xl p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600 mb-1">24/7</p>
                    <p class="text-sm text-gray-500">Dukungan CS</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if(isset($landingContents['contact']))
    <section class="max-w-6xl mx-auto">
        <hr class="section-divider mb-12">
        @php $contactItem = $landingContents['contact'][0]; @endphp
        <div class="text-center max-w-2xl mx-auto reveal-text">
            <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full mb-4">Kontak</span>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $contactItem->title }}</h2>
            <p class="text-gray-600 mb-8">{{ $contactItem->content }}</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="https://wa.me/{{ \App\Models\Setting::getValue('company_phone', '081234567890') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp CS
                </a>
                <a href="mailto:{{ \App\Models\Setting::getValue('company_email', 'support@kirimin.id') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-6 py-3 rounded-xl border border-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email Kami
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- ===== FOOTER SPACER ===== -->
    <div class="h-8"></div>
</div>
@endsection