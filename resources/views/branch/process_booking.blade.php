@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('branch.scan.show') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Scan
        </a>
    </div>

    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Proses Paket Masuk</h1>
            <p class="text-sm text-slate-400 mt-1">Cabang: <span class="text-slate-200 font-medium">{{ $branch->name }}</span></p>
        </div>
        <span class="text-xs font-semibold uppercase px-3 py-1.5 rounded-full bg-blue-950/60 text-blue-400 border border-blue-800/50 mt-1">
            {{ str_replace('_', ' ', $shipment->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Shipment Details --}}
        <div class="space-y-5">
            {{-- Resi & Route --}}
            <div class="glass-panel rounded-2xl border border-slate-800 p-5">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Identitas Paket</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Resi</span>
                        <span class="font-mono font-semibold text-blue-400">{{ $shipment->tracking_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kode Booking</span>
                        <span class="font-mono text-slate-300">{{ $shipment->booking_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Layanan</span>
                        <span class="font-semibold uppercase text-{{ $shipment->service_type === 'express' ? 'amber' : 'slate' }}-400">
                            {{ $shipment->service_type }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rute</span>
                        <span class="text-slate-200">{{ $shipment->origin_city }} → {{ $shipment->destination_city }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Estimasi Berat</span>
                        <span class="text-slate-200">{{ number_format($shipment->estimated_weight, 2) }} kg</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Estimasi Harga</span>
                        <span class="text-white font-semibold">Rp {{ number_format($shipment->estimated_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Sender & Receiver --}}
            <div class="glass-panel rounded-2xl border border-slate-800 p-5">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Pengirim & Penerima</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Pengirim</p>
                        <p class="text-white font-medium">{{ $shipment->sender_name }}</p>
                        <p class="text-slate-400 text-xs mt-0.5">{{ $shipment->sender_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $shipment->sender_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Penerima</p>
                        <p class="text-white font-medium">{{ $shipment->receiver_name }}</p>
                        <p class="text-slate-400 text-xs mt-0.5">{{ $shipment->receiver_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $shipment->receiver_address }}</p>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="glass-panel rounded-2xl border border-slate-800 p-5">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Daftar Barang ({{ $shipment->items->count() }} item)</h2>
                <div class="divide-y divide-slate-800">
                    @foreach($shipment->items as $item)
                    <div class="py-2.5 flex justify-between items-center text-sm">
                        <span class="text-slate-200">{{ $item->item_name }}</span>
                        <span class="text-slate-400">{{ $item->quantity }}x &bull; {{ number_format($item->weight, 2) }}kg</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Payment Status --}}
            @if($shipment->payment)
            <div class="glass-panel rounded-2xl border {{ $shipment->payment->payment_status === 'paid' ? 'border-emerald-900/40' : 'border-orange-900/40' }} p-5">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Status Pembayaran</h2>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-400">Total Tagihan</span>
                    <span class="font-bold text-white">Rp {{ number_format($shipment->payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-sm text-slate-400">Status</span>
                    <span class="text-sm font-semibold {{ $shipment->payment->payment_status === 'paid' ? 'text-emerald-400' : 'text-orange-400' }} uppercase">
                        {{ $shipment->payment->payment_status === 'paid' ? '✓ Lunas' : '⚠ Belum Bayar' }}
                    </span>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions Panel --}}
        <div class="space-y-5">
            {{-- Weigh Form --}}
            @if(in_array($shipment->status, ['waiting_dropoff', 'booking_created']))
            <div class="glass-panel rounded-2xl border border-blue-900/30 p-5">
                <h2 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-1m6 1l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-1m6 1H6"/></svg>
                    Timbang Paket
                </h2>
                <form method="POST" action="{{ route('branch.process-weigh', $shipment) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-2" for="actual_weight">Berat Aktual (kg) *</label>
                        <input id="actual_weight" type="number" name="actual_weight" step="0.1" min="0.1" required
                               placeholder="0.0"
                               class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                        @error('actual_weight')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-300 mb-2" for="notes">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm resize-none"
                                  placeholder="Kondisi paket, catatan khusus..."></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg transition">
                        Simpan & Proses Timbangan
                    </button>
                </form>
            </div>
            @endif

            {{-- Cash Confirm --}}
            @if($shipment->payment && $shipment->payment->payment_status !== 'paid' && in_array($shipment->status, ['weighed', 'waiting_dropoff', 'booking_created']))
            <div class="glass-panel rounded-2xl border border-emerald-900/30 p-5">
                <h2 class="text-base font-semibold text-white mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    Konfirmasi Pembayaran Tunai
                </h2>
                <p class="text-sm text-slate-400 mb-4">
                    Tagihan: <span class="font-bold text-white">Rp {{ number_format($shipment->payment->amount, 0, ',', '.') }}</span>
                </p>
                <form method="POST" action="{{ route('branch.confirm-cash', $shipment) }}" onsubmit="return confirm('Konfirmasi pembayaran tunai telah diterima?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg transition">
                        ✓ Konfirmasi Pembayaran Cash
                    </button>
                </form>
            </div>
            @endif

            {{-- Paid Badge --}}
            @if($shipment->payment && $shipment->payment->payment_status === 'paid' && $shipment->status === 'weighed')
            <div class="glass-panel rounded-2xl border border-emerald-900/30 p-5 text-center">
                <p class="text-emerald-400 font-semibold text-sm">✓ Pembayaran Lunas</p>
                <p class="text-xs text-slate-400 mt-1">Paket siap diterima di gudang dan ditugaskan ke kurir</p>
            </div>
            @endif

            {{-- Receive Transit Button --}}
            @if($shipment->status === 'in_transit' && strtolower($shipment->destination_city) === strtolower($branch->city))
            <div class="glass-panel rounded-2xl border border-blue-900/30 p-5">
                <h2 class="text-base font-semibold text-white mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Terima Paket Transit
                </h2>
                <p class="text-sm text-slate-400 mb-4">
                    Paket transit ini ditujukan ke kota Anda. Konfirmasi kedatangan paket di cabang tujuan.
                </p>
                <form method="POST" action="{{ route('branch.receive-transit', $shipment) }}" onsubmit="return confirm('Terima paket ini di cabang {{ $branch->name }}?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg transition">
                        ✓ Konfirmasi Terima Paket Transit
                    </button>
                </form>
            </div>
            @endif

            {{-- Already processed --}}
            @if(in_array($shipment->status, ['received_at_branch', 'assigned_to_courier', 'out_for_delivery', 'delivered']))
            <div class="glass-panel rounded-2xl border border-slate-700 p-5 text-center">
                <p class="text-slate-300 font-semibold text-sm">Paket Sudah Diproses</p>
                <p class="text-xs text-slate-500 mt-1 capitalize">Status: {{ str_replace('_',' ',$shipment->status) }}</p>
                <a href="{{ route('branch.receipt', $shipment) }}"
                   class="mt-4 inline-flex items-center gap-1 text-sm text-blue-400 hover:text-blue-300 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Cetak Resi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
