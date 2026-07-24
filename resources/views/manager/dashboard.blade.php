@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Manager</h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan operasional seluruh cabang BAZMA Express</p>
            </div>
            <a href="{{ route('manager.report', request()->only(['start_date', 'end_date'])) }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm sm:hidden">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Laporan
            </a>
        </div>

        {{-- Filter Bar --}}
        <div class="card-panel border border-slate-200 rounded-2xl p-5 mb-8">
            <form method="GET" action="{{ route('manager.dashboard') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="text-xs text-slate-600 mb-1.5 block font-medium">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div class="flex-1">
                    <label class="text-xs text-slate-600 mb-1.5 block font-medium">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div class="flex-shrink-0 flex gap-2">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition flex-1 sm:flex-initial shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('manager.dashboard') }}"
                       class="bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 text-sm font-semibold px-5 py-2.5 rounded-xl transition text-center flex-1 sm:flex-initial">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="card-panel rounded-xl p-5 border border-slate-200 col-span-2 lg:col-span-1">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1 font-semibold">Total Pendapatan</p>
                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="card-panel rounded-xl p-5 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1 font-semibold">Cabang</p>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['total_branches'] }}</p>
            </div>
            <div class="card-panel rounded-xl p-5 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1 font-semibold">Karyawan</p>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['total_employees'] }}</p>
            </div>
            <div class="card-panel rounded-xl p-5 border border-slate-200">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1 font-semibold">Total Pengiriman</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_shipments'] }}</p>
            </div>
        </div>

        {{-- Status Pipeline --}}
        <div class="card-panel rounded-2xl border border-slate-200 p-6 mb-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Pipeline Status Pengiriman</h2>
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
            $colorText = ['slate'=>'text-slate-600','yellow'=>'text-amber-600','blue'=>'text-blue-600','violet'=>'text-violet-600','cyan'=>'text-cyan-600','emerald'=>'text-emerald-600','red'=>'text-red-600'];
            $colorBg = ['slate'=>'bg-slate-400','yellow'=>'bg-amber-500','blue'=>'bg-blue-500','violet'=>'bg-violet-500','cyan'=>'bg-cyan-500','emerald'=>'bg-emerald-500','red'=>'bg-red-500'];
            $total = max(1, $stats['total_shipments']);
            @endphp
            <div class="space-y-3">
                @foreach($pipeline as $p)
                @php $val = $stats[$p['key']] ?? 0; $pct = round(($val / $total) * 100); @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-600 font-medium w-32 flex-shrink-0">{{ $p['label'] }}</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="{{ $colorBg[$p['color']] }} h-2 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs {{ $colorText[$p['color']] }} font-semibold w-8 text-right">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Revenue Chart --}}
        @if($monthlyRevenue->isNotEmpty())
        <div class="card-panel rounded-2xl border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Tren Pendapatan Bulanan</h2>
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
            backgroundColor: 'rgba(37, 99, 235, 0.2)',
            borderColor: '#2563eb',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            y: { ticks: { color: '#64748b', font: { size: 11 }, callback: v => 'Rp '+ new Intl.NumberFormat('id-ID').format(v) }, grid: { color: '#f1f5f9' } }
        }
    }
});
@endif
</script>
@endsection
