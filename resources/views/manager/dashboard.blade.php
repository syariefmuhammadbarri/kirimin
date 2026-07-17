@extends('layouts.app')

@section('styles')
<style>
    .nav-link { @apply flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition; }
    .nav-link.active { @apply bg-blue-600/20 text-blue-400 border border-blue-800/40; }
</style>
@endsection

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-52 flex-shrink-0 gap-1 pt-1">
        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider px-3 mb-2">Manager Portal</p>
        <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('manager.branches.index') }}" class="nav-link {{ request()->routeIs('manager.branches.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Cabang
        </a>
        <a href="{{ route('manager.users.index') }}" class="nav-link {{ request()->routeIs('manager.users.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Karyawan
        </a>
        <a href="{{ route('manager.vehicles.index') }}" class="nav-link {{ request()->routeIs('manager.vehicles.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Kendaraan
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.settings.index') }}" class="nav-link {{ request()->routeIs('manager.settings.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
        <a href="{{ route('manager.landing-contents.index') }}" class="nav-link {{ request()->routeIs('manager.landing-contents.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Landing Page
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.report', request()->only(['start_date', 'end_date'])) }}" class="nav-link">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Unduh Laporan
        </a>
    </aside>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Manager</h1>
                <p class="text-sm text-slate-400 mt-1">Ringkasan operasional seluruh cabang BAZMA Express</p>
            </div>
            <a href="{{ route('manager.report', request()->only(['start_date', 'end_date'])) }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-blue-950/50 sm:hidden">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Laporan
            </a>
        </div>

        {{-- Filter Bar --}}
        <div class="glass-panel border border-slate-800 rounded-2xl p-5 mb-8">
            <form method="GET" action="{{ route('manager.dashboard') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="text-xs text-slate-400 mb-1.5 block font-medium">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div class="flex-1">
                    <label class="text-xs text-slate-400 mb-1.5 block font-medium">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div class="flex-shrink-0 flex gap-2">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition flex-1 sm:flex-initial shadow-lg shadow-blue-950/30">
                        Filter
                    </button>
                    <a href="{{ route('manager.dashboard') }}"
                       class="bg-slate-900 border border-slate-800 text-slate-300 hover:bg-slate-800 hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition text-center flex-1 sm:flex-initial">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="glass-panel rounded-xl p-5 border border-slate-800 col-span-2 lg:col-span-1">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Pendapatan</p>
                <p class="text-2xl font-bold text-emerald-400">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5 border border-slate-800">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Cabang</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_branches'] }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5 border border-slate-800">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Karyawan</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_employees'] }}</p>
            </div>
            <div class="glass-panel rounded-xl p-5 border border-slate-800">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Pengiriman</p>
                <p class="text-3xl font-bold text-white">{{ $stats['total_shipments'] }}</p>
            </div>
        </div>

        {{-- Status Pipeline --}}
        <div class="glass-panel rounded-2xl border border-slate-800 p-6 mb-6">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-5">Pipeline Status Pengiriman</h2>
            @php
            $pipeline = [
                ['label' => 'Dibuat', 'key' => 'booking_created', 'color' => 'slate'],
                ['label' => 'Menunggu Drop', 'key' => 'waiting_dropoff', 'color' => 'yellow'],
                ['label' => 'Ditimbang', 'key' => 'weighed', 'color' => 'blue'],
                ['label' => 'Ditugaskan', 'key' => 'assigned', 'color' => 'violet'],
                ['label' => 'Dalam Transit', 'key' => 'transit', 'color' => 'cyan'],
                ['label' => 'Terkirim', 'key' => 'delivered', 'color' => 'emerald'],
                ['label' => 'Gagal', 'key' => 'failed', 'color' => 'red'],
            ];
            $colorText = ['slate'=>'text-slate-300','yellow'=>'text-yellow-400','blue'=>'text-blue-400','violet'=>'text-violet-400','cyan'=>'text-cyan-400','emerald'=>'text-emerald-400','red'=>'text-red-400'];
            $colorBg = ['slate'=>'bg-slate-700','yellow'=>'bg-yellow-500','blue'=>'bg-blue-500','violet'=>'bg-violet-500','cyan'=>'bg-cyan-500','emerald'=>'bg-emerald-500','red'=>'bg-red-500'];
            $total = max(1, $stats['total_shipments']);
            @endphp
            <div class="space-y-3">
                @foreach($pipeline as $p)
                @php $val = $stats[$p['key']] ?? 0; $pct = round(($val / $total) * 100); @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-32 flex-shrink-0">{{ $p['label'] }}</span>
                    <div class="flex-1 bg-slate-900 rounded-full h-2 overflow-hidden">
                        <div class="{{ $colorBg[$p['color']] }} h-2 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs {{ $colorText[$p['color']] }} font-semibold w-8 text-right">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Revenue Chart --}}
        @if($monthlyRevenue->isNotEmpty())
        <div class="glass-panel rounded-2xl border border-slate-800 p-6">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-5">Tren Pendapatan Bulanan</h2>
            <div class="relative h-48">
                <canvas id="revenue-chart"></canvas>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($monthlyRevenue->isNotEmpty())
const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: @json($monthlyRevenue->pluck('total')),
            backgroundColor: 'rgba(59,130,246,0.3)',
            borderColor: 'rgba(59,130,246,0.8)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { color: '#1e293b' } },
            y: { ticks: { color: '#64748b', font: { size: 11 }, callback: v => 'Rp '+ new Intl.NumberFormat('id-ID').format(v) }, grid: { color: '#1e293b' } }
        }
    }
});
@endif
</script>
@endsection
