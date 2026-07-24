@extends('layouts.app')

@section('styles')
<style>
    .kpi-card { @apply card-panel rounded-2xl p-6 relative overflow-hidden border border-slate-200; }
    .kpi-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
    }
    .kpi-card-green::before { background: linear-gradient(to right, #10b981, #34d399); }
    .kpi-card-blue::before { background: linear-gradient(to right, #2563eb, #60a5fa); }
    .kpi-card-violet::before { background: linear-gradient(to right, #7c3aed, #a78bfa); }
    .kpi-card-amber::before { background: linear-gradient(to right, #d97706, #fbbf24); }
    .rank-row { @apply flex items-center gap-4 py-3 border-b border-slate-100; }
    .rank-row:last-child { @apply border-b-0; }
</style>
@endsection

@section('content')
{{-- Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-1">Executive Dashboard</p>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Overview Bisnis</h1>
        <p class="text-sm text-slate-500 mt-1">Ringkasan kinerja operasional BAZMA Express — {{ now()->format('d F Y') }}</p>
    </div>
    <a href="{{ route('owner.export-report', request()->only(['start_date', 'end_date'])) }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Unduh Laporan Strategis
    </a>
</div>

{{-- Filter Bar --}}
<div class="card-panel border border-slate-200 rounded-2xl p-5 mb-8">
    <form method="GET" action="{{ route('owner.dashboard') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
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
            <a href="{{ route('owner.dashboard') }}"
               class="bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 text-sm font-semibold px-5 py-2.5 rounded-xl transition text-center flex-1 sm:flex-initial">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <div class="kpi-card kpi-card-green">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total Pendapatan</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-2">Dari transaksi yang telah lunas</p>
    </div>

    <div class="kpi-card kpi-card-blue">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total Pengiriman</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($totalShipments) }}</p>
        <p class="text-xs text-slate-500 mt-2">Seluruh status pengiriman</p>
    </div>

    <div class="kpi-card kpi-card-violet">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 border border-violet-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tingkat Keberhasilan</p>
        <p class="text-3xl font-bold text-violet-600 mt-1">{{ number_format($successRate, 1) }}%</p>
        <p class="text-xs text-slate-500 mt-2">Paket berhasil terkirim</p>
    </div>

    <div class="kpi-card kpi-card-amber">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Cabang Aktif</p>
        <p class="text-3xl font-bold text-amber-600 mt-1">{{ $branchesRanking->count() }}</p>
        <p class="text-xs text-slate-500 mt-2">Tersebar di berbagai kota</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    {{-- Monthly Revenue Chart --}}
    <div class="xl:col-span-2 card-panel rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-5">Tren Pendapatan Bulanan</h2>
        @if($monthlyRevenue->isNotEmpty())
        <div class="relative h-52">
            <canvas id="monthly-chart"></canvas>
        </div>
        @else
        <div class="h-52 flex items-center justify-center text-slate-400 text-sm">
            Belum ada data pendapatan
        </div>
        @endif
    </div>

    {{-- Status Distribution --}}
    <div class="card-panel rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-5">Distribusi Status</h2>
        @php
        $statusLabels = [
            'booking_created' => ['Dibuat', 'slate'],
            'waiting_dropoff' => ['Menunggu', 'yellow'],
            'weighed' => ['Ditimbang', 'blue'],
            'payment_pending' => ['Pend. Bayar', 'orange'],
            'received_at_branch' => ['Di Cabang', 'indigo'],
            'assigned_to_courier' => ['Ditugaskan', 'violet'],
            'out_for_delivery' => ['Dalam Transit', 'cyan'],
            'delivered' => ['Terkirim', 'emerald'],
            'gagal_kirim' => ['Gagal', 'red'],
        ];
        $colorText = ['slate'=>'text-slate-600','yellow'=>'text-amber-600','blue'=>'text-blue-600','orange'=>'text-orange-600','indigo'=>'text-indigo-600','violet'=>'text-violet-600','cyan'=>'text-cyan-600','emerald'=>'text-emerald-600','red'=>'text-red-600'];
        $colorBg = ['slate'=>'bg-slate-400','yellow'=>'bg-amber-500','blue'=>'bg-blue-500','orange'=>'bg-orange-500','indigo'=>'bg-indigo-500','violet'=>'bg-violet-500','cyan'=>'bg-cyan-500','emerald'=>'bg-emerald-500','red'=>'bg-red-500'];
        $total = max(1, $totalShipments);
        @endphp
        <div class="space-y-3">
            @foreach($statusDistribution as $status => $count)
            @php
            $info = $statusLabels[$status] ?? [$status, 'slate'];
            $pct = round(($count / $total) * 100);
            @endphp
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600 font-medium">{{ $info[0] }}</span>
                    <span class="{{ $colorText[$info[1]] }} font-semibold">{{ $count }}</span>
                </div>
                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="{{ $colorBg[$info[1]] }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Branch Ranking --}}
    <div class="card-panel rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Ranking Cabang
        </h2>
        @foreach($branchesRanking as $i => $branch)
        <div class="rank-row">
            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                {{ $i === 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                {{ $i + 1 }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ $branch->name }}</p>
                <p class="text-xs text-slate-500">{{ $branch->city }} &bull; {{ $branch->shipments_count }} paket</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($branch->revenue, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
        @if($branchesRanking->isEmpty())
        <p class="text-sm text-slate-400 text-center py-4">Belum ada data</p>
        @endif
    </div>

    {{-- Top Customers --}}
    <div class="card-panel rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Top Pelanggan
        </h2>
        @foreach($topCustomers as $i => $customer)
        <div class="rank-row">
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ $customer->name }}</p>
                <p class="text-xs text-slate-500">{{ $customer->shipments_count }} pengiriman</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-semibold text-blue-600">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
        @if($topCustomers->isEmpty())
        <p class="text-sm text-slate-400 text-center py-4">Belum ada data</p>
        @endif
    </div>

    {{-- Top Couriers --}}
    <div class="card-panel rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Top Kurir
        </h2>
        @foreach($topCouriers as $i => $courier)
        <div class="rank-row">
            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                {{ $i === 0 ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                {{ $i + 1 }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ $courier->name }}</p>
                <p class="text-xs text-slate-500">{{ $courier->delivered_jobs }}/{{ $courier->total_jobs }} terkirim</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-semibold text-cyan-600">{{ number_format($courier->success_rate, 0) }}%</p>
            </div>
        </div>
        @endforeach
        @if($topCouriers->isEmpty())
        <p class="text-sm text-slate-400 text-center py-4">Belum ada data</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($monthlyRevenue->isNotEmpty())
const ctx = document.getElementById('monthly-chart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 200);
gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Pendapatan',
            data: @json($monthlyRevenue->pluck('total')),
            borderColor: '#2563eb',
            backgroundColor: gradient,
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: '#2563eb',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                titleColor: '#f8fafc',
                bodyColor: '#f8fafc',
                callbacks: {
                    label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                }
            }
        },
        scales: {
            x: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            y: { ticks: { color: '#64748b', font: { size: 11 }, callback: v => 'Rp '+ new Intl.NumberFormat('id-ID').format(v) }, grid: { color: '#f1f5f9' } }
        }
    }
});
@endif
</script>
@endsection
