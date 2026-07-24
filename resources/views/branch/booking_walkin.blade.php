@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-500 hover:text-blue-600 flex items-center gap-1 transition font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Booking Walk-in</h1>
        <p class="text-sm text-slate-500 mt-1">Buat booking langsung untuk pelanggan yang datang ke outlet.</p>
    </div>

    @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1 font-medium">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('branch.booking.walkin.store') }}" id="walkin-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Kolom Kiri: Rute & Customer --}}
            <div class="space-y-5">
                {{-- Rute --}}
                <div class="card-panel rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Rute Pengiriman</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="origin_city">Kota Asal *</label>
                            <select id="origin_city" name="origin_city" required
                                    class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Pilih Kota Asal</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->name }}" {{ old('origin_city') === $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="destination_city">Kota Tujuan *</label>
                            <select id="destination_city" name="destination_city" required
                                    class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Pilih Kota Tujuan</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->name }}" {{ old('destination_city') === $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Layanan *</label>
                            <div class="flex gap-3">
                                <label class="flex-1 flex items-center gap-2 cursor-pointer border border-slate-200 rounded-lg px-3 py-2.5 hover:border-slate-300 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                                    <input type="radio" name="service_type" value="regular" {{ old('service_type', 'regular') === 'regular' ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700 font-medium">Regular</span>
                                </label>
                                <label class="flex-1 flex items-center gap-2 cursor-pointer border border-slate-200 rounded-lg px-3 py-2.5 hover:border-slate-300 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                                    <input type="radio" name="service_type" value="express" {{ old('service_type') === 'express' ? 'checked' : '' }}>
                                    <span class="text-sm text-blue-700 font-bold">Express</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer (opsional) --}}
                <div class="card-panel rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Tautkan ke Akun Customer <span class="font-normal text-slate-400">(opsional)</span></h2>
                    <select name="customer_id" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">— Pelanggan tanpa akun / anonim —</option>
                        @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                            {{ $cust->user?->name ?? 'Tanpa nama' }} ({{ $cust->phone ?: $cust->user?->email }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1.5">Jika tidak dipilih, booking akan dibuat sebagai walk-in anonim.</p>
                </div>

                {{-- Pengirim --}}
                <div class="card-panel rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Data Pengirim</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="sender_name">Nama *</label>
                            <input id="sender_name" type="text" name="sender_name" value="{{ old('sender_name') }}" required
                                   class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="sender_phone">No. HP *</label>
                            <input id="sender_phone" type="tel" name="sender_phone" value="{{ old('sender_phone') }}" required
                                   class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="sender_address">Alamat *</label>
                            <textarea id="sender_address" name="sender_address" rows="2" required
                                      class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none">{{ old('sender_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Penerima & Barang --}}
            <div class="space-y-5">
                {{-- Penerima --}}
                <div class="card-panel rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Data Penerima</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="receiver_name">Nama *</label>
                            <input id="receiver_name" type="text" name="receiver_name" value="{{ old('receiver_name') }}" required
                                   class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="receiver_phone">No. HP *</label>
                            <input id="receiver_phone" type="tel" name="receiver_phone" value="{{ old('receiver_phone') }}" required
                                   class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="receiver_address">Alamat *</label>
                            <textarea id="receiver_address" name="receiver_address" rows="2" required
                                      class="w-full px-3 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none">{{ old('receiver_address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Daftar Barang (dynamic) --}}
                <div class="card-panel rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Barang</h2>
                        <button type="button" onclick="addItem()"
                                class="text-xs text-blue-600 hover:text-blue-700 border border-blue-200 bg-blue-50 rounded-lg px-2.5 py-1 transition font-semibold">
                            + Tambah Barang
                        </button>
                    </div>
                    <div id="items-container" class="space-y-3">
                        <div class="item-row grid grid-cols-5 gap-2" data-index="0">
                            <input type="text" name="items[0][name]" placeholder="Nama barang" required
                                   class="col-span-2 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="items[0][quantity]" placeholder="Qty" min="1" value="1" required
                                   class="col-span-1 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="items[0][weight]" placeholder="kg" step="0.1" min="0.1" required
                                   class="col-span-1 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="col-span-1 flex items-center justify-center">
                                <span class="text-xs text-slate-400">—</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-3">Kolom: Nama | Qty | Berat (kg)</p>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    Buat Booking Walk-in
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const container = document.getElementById('items-container');
    const idx = itemIndex++;
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-5 gap-2';
    row.dataset.index = idx;
    row.innerHTML = `
        <input type="text" name="items[${idx}][name]" placeholder="Nama barang" required
               class="col-span-2 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="number" name="items[${idx}][quantity]" placeholder="Qty" min="1" value="1" required
               class="col-span-1 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="number" name="items[${idx}][weight]" placeholder="kg" step="0.1" min="0.1" required
               class="col-span-1 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.item-row').remove()"
                    class="text-red-500 hover:text-red-700 text-xs font-bold transition">✕</button>
        </div>`;
    container.appendChild(row);
}
</script>
@endsection
