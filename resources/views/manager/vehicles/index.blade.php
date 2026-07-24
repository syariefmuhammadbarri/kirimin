@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Kendaraan</h1>
                <p class="text-sm text-slate-500 mt-1">Atur armada kendaraan dan asosiasi dengan kurir</p>
            </div>
            <a href="{{ route('manager.vehicles.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kendaraan
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 px-5 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <div class="card-panel rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-base font-bold text-slate-900">Daftar Kendaraan</h2>
                <span class="text-xs text-slate-500 font-medium">{{ $vehicles->count() }} unit</span>
            </div>

            @if($vehicles->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-14 h-14 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <p class="text-slate-500 font-medium">Belum ada kendaraan terdaftar</p>
                <p class="text-slate-400 text-sm mt-1">Tambahkan kendaraan baru untuk mulai mengelola armada</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 bg-slate-50/50">
                            <th class="px-6 py-3 text-left font-semibold">Plat Nomor</th>
                            <th class="px-6 py-3 text-left font-semibold">Tipe</th>
                            <th class="px-6 py-3 text-left font-semibold">Kurir</th>
                            <th class="px-6 py-3 text-left font-semibold">Cabang</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($vehicles as $vehicle)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-slate-900">{{ $vehicle->plate_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold uppercase px-2.5 py-1 rounded-full border
                                    {{ $vehicle->type === 'truck' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                    {{ $vehicle->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                {{ $vehicle->courier->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $vehicle->courier->branch->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.vehicles.edit', $vehicle) }}"
                                       class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-lg font-medium transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.vehicles.destroy', $vehicle) }}"
                                          onsubmit="return confirm('Hapus kendaraan {{ $vehicle->plate_number }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg font-medium transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection