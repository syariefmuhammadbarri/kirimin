@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2.5 py-1 rounded-full tracking-wide; }
    .status-pending { @apply bg-yellow-50 text-yellow-700 border border-yellow-200; }
    .status-assigned { @apply bg-blue-50 text-blue-700 border border-blue-200; }
    .status-completed { @apply bg-emerald-50 text-emerald-700 border border-emerald-200; }
    .status-cancelled { @apply bg-red-50 text-red-700 border border-red-200; }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Penugasan Kurir</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $branch->name }} &bull; {{ $branch->city }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">← Kembali ke Dashboard</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Tugas</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['active'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Aktif</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Selesai</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Dibatalkan</p>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card-panel rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-black/5 bg-slate-50/50">
                        <th class="text-left font-semibold text-slate-600 p-4">Paket</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Kurir</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Ditugaskan Oleh</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Tanggal</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Status</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr class="border-b border-black/5 hover:bg-slate-50/50 transition">
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ $assignment->shipment->tracking_number ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $assignment->shipment->receiver_name ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ $assignment->courier->name }}</div>
                                <div class="text-xs text-slate-500">{{ $assignment->courier->email }}</div>
                            </td>
                            <td class="p-4 text-slate-600">{{ $assignment->assignor->name }}</td>
                            <td class="p-4 text-slate-600">
                                @if ($assignment->assigned_at)
                                    {{ $assignment->assigned_at->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="status-badge status-{{ $assignment->status }}">{{ $assignment->status }}</span>
                            </td>
                            <td class="p-4 text-slate-500 text-xs max-w-[150px] truncate">{{ $assignment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <div class="empty-state">
                                    <svg class="h-12 w-12 mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada penugasan kurir</p>
                                    <p class="text-xs mt-1">Tugaskan kurir dari halaman dashboard untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Courier List -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Kurir Tersedia</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($couriers as $courier)
                <div class="card-panel rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-sm font-bold text-blue-600">{{ substr($courier->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $courier->name }}</p>
                                <p class="text-xs text-slate-500">{{ $courier->email }}</p>
                            </div>
                        </div>
                        @php
                            $activeJobs = $assignments->where('courier_id', $courier->id)->whereIn('status', ['pending', 'assigned'])->count();
                        @endphp
                        <span class="text-xs font-semibold {{ $activeJobs >= 5 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $activeJobs }}/5 aktif
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(($activeJobs / 5) * 100, 100) }}%"></div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-400 py-8">
                    <p>Tidak ada kurir terdaftar di cabang ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection