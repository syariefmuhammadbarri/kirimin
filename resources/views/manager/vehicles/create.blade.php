@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('manager.vehicles.index') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Kendaraan
        </a>
    </div>

    <h1 class="text-2xl font-bold text-white mb-6">Tambah Kendaraan Baru</h1>

    <form method="POST" action="{{ route('manager.vehicles.store') }}" class="glass-panel rounded-2xl border border-slate-800 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2" for="plate_number">Plat Nomor <span class="text-red-400">*</span></label>
            <input id="plate_number" type="text" name="plate_number" value="{{ old('plate_number') }}" required
                   placeholder="Contoh: B 1234 XYZ"
                   class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm font-mono uppercase">
            @error('plate_number')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-3">Tipe Kendaraan <span class="text-red-400">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="motor" class="sr-only peer" {{ old('type', 'motor') === 'motor' ? 'checked' : '' }}>
                    <div class="glass-panel border-2 border-slate-700 peer-checked:border-cyan-500 rounded-xl p-4 text-center transition">
                        <svg class="w-8 h-8 text-cyan-400 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <div class="font-semibold text-white text-sm">Motor</div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="truck" class="sr-only peer" {{ old('type') === 'truck' ? 'checked' : '' }}>
                    <div class="glass-panel border-2 border-slate-700 peer-checked:border-amber-500 rounded-xl p-4 text-center transition">
                        <svg class="w-8 h-8 text-amber-400 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <div class="font-semibold text-white text-sm">Truck</div>
                    </div>
                </label>
            </div>
            @error('type')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2" for="courier_id">Ditugaskan ke Kurir (opsional)</label>
            <select id="courier_id" name="courier_id"
                    class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                <option value="">-- Pilih Kurir --</option>
                @foreach($couriers as $courier)
                    <option value="{{ $courier->id }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>
                        {{ $courier->name }} ({{ $courier->branch->name ?? 'Tanpa Cabang' }})
                    </option>
                @endforeach
            </select>
            @error('courier_id')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/50 transition">
            Simpan Kendaraan
        </button>
    </form>
</div>
@endsection