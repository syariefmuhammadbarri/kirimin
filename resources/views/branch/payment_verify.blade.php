@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-600 flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran Kasir</h1>
        <p class="text-sm text-slate-500 mt-1">Konfirmasi pembayaran tunai untuk booking walk-in.</p>
    </div>

    @if($errors->any())
    <x-alert type="error">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </x-alert>
    @endif

    {{-- Rincian Paket --}}
    <div class="glass-panel rounded-2xl border border-slate-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">📦 Rincian Paket</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">Nomor Resi</span>
                <span class="font-mono font-semibold text-slate-800">{{ $shipment->tracking_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Kode Booking</span>
                <span class="font-mono text-slate-600">{{ $shipment->booking_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Pengirim</span>
                <span class="font-medium text-slate-800">{{ $shipment->sender_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Penerima</span>
                <span class="font-medium text-slate-800">{{ $shipment->receiver_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Rute</span>
                <span class="text-slate-700">{{ $shipment->origin_city }} → {{ $shipment->destination_city }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Layanan</span>
                <span class="font-semibold uppercase {{ $shipment->service_type === 'express' ? 'text-amber-600' : 'text-slate-600' }}">
                    {{ $shipment->service_type }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Estimasi Berat</span>
                <span>{{ number_format($shipment->estimated_weight, 2) }} kg</span>
            </div>
            <div class="border-t border-slate-100 pt-3 flex justify-between">
                <span class="text-base font-semibold text-slate-700">Total Tagihan</span>
                <span class="text-lg font-bold text-slate-900">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Daftar Barang --}}
    @if($shipment->items->isNotEmpty())
    <div class="glass-panel rounded-2xl border border-slate-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">📋 Daftar Barang</h2>
        <div class="divide-y divide-slate-100">
            @foreach($shipment->items as $item)
            <div class="py-2.5 flex justify-between items-center text-sm">
                <span class="text-slate-700">{{ $item->item_name }}</span>
                <span class="text-slate-500">{{ $item->quantity }}x • {{ number_format($item->weight, 2) }}kg</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Cashier Settlement Form --}}
    <div class="glass-panel rounded-2xl border border-emerald-200 bg-white p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            Input Nominal Pembayaran Tunai
        </h2>
        <p class="text-sm text-slate-500 mb-5">Masukkan nominal uang yang diterima dari pelanggan. Kembalian akan dihitung otomatis.</p>

        <form method="POST" action="{{ route('branch.payment.process', $shipment) }}" id="cash-settlement-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2" for="amount_paid">
                    Uang Diterima (Rp) <span class="text-red-500">*</span>
                </label>
                <input id="amount_paid" type="number" name="amount_paid"
                       min="{{ $shipment->total_price }}" step="1000" required
                       placeholder="{{ (int) $shipment->total_price }}"
                       oninput="hitungKembalian({{ $shipment->total_price }})"
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-base font-mono">
                @error('amount_paid')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Kembalian Display --}}
            <div id="kembalian-box" class="hidden rounded-lg bg-emerald-50 border border-emerald-200 p-4 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Kembalian</span>
                    <span id="kembalian-val" class="text-lg font-bold text-emerald-700">Rp 0</span>
                </div>
            </div>
            <div id="kurang-box" class="hidden rounded-lg bg-red-50 border border-red-200 p-4 mb-4">
                <p class="text-sm text-red-600">⚠ Nominal yang diterima kurang dari total tagihan.</p>
            </div>
            <div id="pas-box" class="hidden rounded-lg bg-blue-50 border border-blue-200 p-4 mb-4">
                <p class="text-sm text-blue-600">💵 Pembayaran pas. Tidak ada kembalian.</p>
            </div>

            <button type="submit" id="submit-btn"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                ✓ Konfirmasi Pembayaran & Cetak Resi
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function hitungKembalian(tagihan) {
    const input = document.getElementById('amount_paid');
    const kembalianBox = document.getElementById('kembalian-box');
    const kurangBox = document.getElementById('kurang-box');
    const pasBox = document.getElementById('pas-box');
    const kembalianVal = document.getElementById('kembalian-val');
    const submitBtn = document.getElementById('submit-btn');

    const dibayar = parseFloat(input.value) || 0;
    const kembalian = dibayar - tagihan;

    kembalianBox.classList.add('hidden');
    kurangBox.classList.add('hidden');
    pasBox.classList.add('hidden');

    if (dibayar <= 0) {
        submitBtn.disabled = false;
        return;
    }

    if (kembalian < 0) {
        kurangBox.classList.remove('hidden');
        submitBtn.disabled = true;
    } else if (kembalian === 0) {
        pasBox.classList.remove('hidden');
        submitBtn.disabled = false;
    } else {
        kembalianBox.classList.remove('hidden');
        kembalianVal.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        submitBtn.disabled = false;
    }
}
</script>
@endsection