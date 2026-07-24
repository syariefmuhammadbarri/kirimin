@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
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
        <span class="text-xs font-semibold uppercase px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 mt-1">
            {{ str_replace('_', ' ', $shipment->status) }}
        </span>
    </div>

    {{-- Status & Location Info --}}
    <div class="glass-panel rounded-2xl border border-slate-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-3">📍 Posisi Paket Saat Ini</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <span class="text-xs text-slate-500 block mb-1">Lokasi</span>
                <span class="font-semibold text-slate-800">
                    @if(in_array($shipment->status, ['in_transit']))
                        Dalam perjalanan ke {{ $shipment->nextBranch?->name ?? $shipment->destination_city }}
                    @elseif($shipment->courier)
                        Bersama Kurir {{ $shipment->courier->name }}
                    @else
                        {{ $branch->name }} ({{ $branch->city }})
                    @endif
                </span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <span class="text-xs text-slate-500 block mb-1">Status</span>
                <span class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $shipment->status) }}</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <span class="text-xs text-slate-500 block mb-1">Kurir</span>
                <span class="font-semibold text-slate-800">{{ $shipment->courier?->name ?? 'Belum ditugaskan' }}</span>
            </div>
        </div>
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
                        <span class="font-mono font-semibold text-slate-700">{{ $shipment->tracking_number }}</span>
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
                    @if($shipment->actual_weight)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Berat Aktual</span>
                        <span class="text-white font-semibold">{{ number_format($shipment->actual_weight, 2) }} kg</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Estimasi Harga</span>
                        <span class="text-white font-semibold">Rp {{ number_format($shipment->estimated_price, 0, ',', '.') }}</span>
                    </div>
                    @if($shipment->actual_price)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Harga Aktual</span>
                        <span class="text-white font-semibold">Rp {{ number_format($shipment->actual_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
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
                    <span class="text-sm font-semibold text-slate-700 uppercase">
                        {{ $shipment->payment->payment_status === 'paid' ? '✓ Lunas' : '⚠ Belum Bayar' }}
                    </span>
                </div>
            </div>
            @endif

            {{-- Timeline Tracking --}}
            <div class="glass-panel rounded-2xl border border-slate-800 p-5">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">📋 Riwayat Perpindahan</h2>
                @php $trackings = $shipment->trackings()->orderBy('tracked_at', 'desc')->get(); @endphp
                @if($trackings->isNotEmpty())
                <div class="relative pl-6 border-l-2 border-slate-200 space-y-5 ml-3">
                    @foreach($trackings as $tracking)
                    <div class="relative">
                        <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-white 
                            @if($loop->first) bg-blue-600 @else bg-slate-300 @endif"></div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full font-medium">
                                {{ $tracking->tracked_at->format('d M Y H:i:s') }}
                            </span>
                            <div class="text-sm font-semibold text-slate-900">{{ $tracking->location }}</div>
                            <p class="text-xs text-slate-500">{{ $tracking->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-500">Belum ada riwayat perpindahan.</p>
                @endif
            </div>
        </div>

        {{-- Actions Panel --}}
        <div class="space-y-5">
            {{-- Weigh Form --}}
            @if(in_array($shipment->status, ['waiting_dropoff', 'booking_created']))
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-1m6 1l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-1m6 1H6"/></svg>
                    Timbang Paket
                </h2>
                <form method="POST" action="{{ route('branch.process-weigh', $shipment) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2" for="actual_weight">Berat Aktual (kg) *</label>
                        <input id="actual_weight" type="number" name="actual_weight" step="0.1" min="0.1" required
                               placeholder="0.0"
                               class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 transition text-sm">
                        @error('actual_weight')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-700 mb-2" for="notes">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 transition text-sm resize-none"
                                  placeholder="Kondisi paket, catatan khusus..."></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                        Simpan & Proses Timbangan
                    </button>
                </form>
            </div>
            @endif

            {{-- Cash Confirm --}}
            @if($shipment->payment && $shipment->payment->payment_status !== 'paid' && in_array($shipment->status, ['weighed', 'waiting_dropoff', 'booking_created']))
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    Konfirmasi Pembayaran Tunai
                </h2>
                @if($shipment->fulfillment_type === 'pickup')
                    <div class="rounded-lg bg-amber-950/40 border border-amber-800/50 p-3 mt-2">
                        <p class="text-sm text-amber-400 flex items-start gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            Paket ini menggunakan layanan <strong>jemput (pickup)</strong>. Pembayaran tunai di outlet tidak diperbolehkan.
                        </p>
                    </div>
                @else
                    @php $tagihan = $shipment->payment->amount ?? $shipment->total_price; @endphp
                    <p class="text-sm text-slate-600 mb-4">
                        Total Tagihan: <span class="font-bold text-slate-800">Rp {{ number_format($tagihan, 0, ',', '.') }}</span>
                    </p>
                    <form method="POST" action="{{ route('branch.confirm-cash', $shipment) }}" id="cash-form">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-slate-700 mb-2" for="paid_amount">
                                Uang Diterima (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input id="paid_amount" type="number" name="paid_amount"
                                   min="{{ $tagihan }}" step="1000" required
                                   placeholder="{{ $tagihan }}"
                                   oninput="hitungKembalian({{ $tagihan }})"
                                   class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 transition text-sm">
                        </div>
                        <div id="kembalian-box" class="hidden rounded-lg bg-emerald-950/30 border border-emerald-800/40 p-3 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Kembalian</span>
                                <span id="kembalian-val" class="font-bold text-emerald-400">Rp 0</span>
                            </div>
                        </div>
                        <div id="kurang-box" class="hidden rounded-lg bg-red-950/30 border border-red-800/40 p-3 mb-4">
                            <p class="text-sm text-red-400">⚠ Nominal kurang dari tagihan.</p>
                        </div>
                        <button type="submit" id="cash-submit-btn"
                                class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                            ✓ Konfirmasi Pembayaran Cash
                        </button>
                    </form>
                @endif
            </div>
            @endif

            @php
                $destCityClean = trim(strtolower($shipment->destination_city));
                $branchCityClean = trim(strtolower($branch->city));
                $cleanDestWithoutPrefix = str_replace(['kota ', 'kabupaten '], '', $destCityClean);
                $cleanBranchWithoutPrefix = str_replace(['kota ', 'kabupaten '], '', $branchCityClean);
                $isFinalBranch = ($cleanBranchWithoutPrefix === $cleanDestWithoutPrefix) || str_contains($destCityClean, $branchCityClean) || str_contains($branchCityClean, $cleanDestWithoutPrefix);
            @endphp

            {{-- Final Destination Notice --}}
            @if($isFinalBranch && in_array($shipment->status, ['received_at_branch', 'weighed']) && $shipment->payment?->payment_status === 'paid')
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 mb-5">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-800">Paket Telah Tiba di Cabang Tujuan Akhir ({{ $branch->name }})</h3>
                        <p class="text-xs text-emerald-700 mt-1">Paket ini tidak perlu dikirim transit lagi. Silakan tugaskan kurir lokal di bawah ini untuk pengantaran langsung ke alamat penerima.</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Send Transit (Hanya tampil jika BELUM di cabang tujuan akhir) --}}
            @if(!$isFinalBranch && in_array($shipment->status, ['received_at_branch', 'weighed']) && $shipment->payment?->payment_status === 'paid')
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Kirim Transit ke Cabang Lain
                </h2>
                <form method="POST" action="{{ route('branch.send-transit', $shipment) }}">
                    @csrf
                    <div class="mb-4">
                        @php
                            $allOtherBranches = (\App\Models\Branch::all())->reject(fn($b) => $b->id === $branch->id);
                            if (isset($suggestedNextBranch) && $suggestedNextBranch) {
                                $sortedBranches = $allOtherBranches->sortByDesc(fn($b) => $b->id === $suggestedNextBranch->id);
                            } else {
                                $sortedBranches = $allOtherBranches;
                            }
                        @endphp
                        <select name="next_branch_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 transition text-sm">
                            <option value="">Pilih Cabang Tujuan</option>
                            @foreach($sortedBranches as $b)
                                @php $isSuggested = isset($suggestedNextBranch) && $suggestedNextBranch && $b->id === $suggestedNextBranch->id; @endphp
                                <option value="{{ $b->id }}" {{ $isSuggested ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->city }}) {{ $isSuggested ? '⭐ (disarankan)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                        🚚 Kirim Transit
                    </button>
                </form>
            </div>
            @endif

            {{-- Receive Transit --}}
            @if($shipment->status === 'in_transit' && $shipment->next_branch_id === $branch->id)
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-base font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Terima Paket Transit
                </h2>
                <p class="text-sm text-slate-600 mb-4">
                    Paket transit dari {{ $shipment->branch?->name ?? $shipment->origin_city }} telah tiba. Konfirmasi penerimaan.
                </p>
                <form method="POST" action="{{ route('branch.receive-transit', $shipment) }}" onsubmit="return confirm('Terima paket ini di {{ $branch->name }}?')">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                        ✓ Konfirmasi Terima Paket Transit
                    </button>
                </form>
            </div>
            @endif

            {{-- Assign Pickup Courier --}}
            @if($shipment->status === 'pickup_scheduled')
            <div class="glass-panel rounded-2xl border border-amber-200 p-5">
                <h2 class="text-base font-semibold text-amber-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Assign Kurir Pickup
                </h2>
                <p class="text-sm text-slate-600 mb-4">Tugaskan kurir untuk menjemput paket dari alamat pengirim.</p>
                <form method="POST" action="{{ route('branch.assign-pickup-courier', $shipment) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="courier_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 transition text-sm">
                            <option value="">Pilih Kurir</option>
                            @foreach($couriers ?? \App\Models\User::role('kurir')->where('branch_id', $branch->id)->get() as $kurir)
                            <option value="{{ $kurir->id }}">{{ $kurir->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 transition text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition">
                        🛵 Assign Kurir Pickup
                    </button>
                </form>
            </div>
            @endif

            {{-- Assign Delivery Courier --}}
            @if(in_array($shipment->status, ['received_at_branch', 'weighed']) && $shipment->payment?->payment_status === 'paid')
            <div class="glass-panel rounded-2xl border border-blue-200 p-5">
                <h2 class="text-base font-semibold text-blue-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Assign Kurir Delivery
                </h2>
                <p class="text-sm text-slate-600 mb-4">Tugaskan kurir untuk mengantarkan paket ke alamat penerima.</p>
                <form method="POST" action="{{ route('branch.assign-courier', $shipment) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="courier_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                            <option value="">Pilih Kurir</option>
                            @foreach($couriers ?? \App\Models\User::role('kurir')->where('branch_id', $branch->id)->get() as $kurir)
                            <option value="{{ $kurir->id }}">{{ $kurir->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        📦 Assign Kurir Delivery
                    </button>
                </form>
            </div>
            @endif

            {{-- Print Receipt --}}
            @if(in_array($shipment->status, ['received_at_branch', 'assigned_to_courier', 'out_for_delivery', 'delivered']))
            <div class="glass-panel rounded-2xl border border-slate-200 p-5 text-center">
                <p class="text-slate-700 font-semibold text-sm">Paket Sudah Diproses</p>
                <p class="text-xs text-slate-600 mt-1 capitalize">Status: {{ str_replace('_',' ',$shipment->status) }}</p>
                <a href="{{ route('branch.receipt', $shipment) }}"
                   class="mt-4 inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Cetak Resi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function hitungKembalian(tagihan) {
    const input = document.getElementById('paid_amount');
    const kembalianBox = document.getElementById('kembalian-box');
    const kurangBox = document.getElementById('kurang-box');
    const kembalianVal = document.getElementById('kembalian-val');
    const submitBtn = document.getElementById('cash-submit-btn');

    const dibayar = parseFloat(input.value) || 0;
    const kembalian = dibayar - tagihan;

    kembalianBox.classList.add('hidden');
    kurangBox.classList.add('hidden');

    if (dibayar <= 0) {
        submitBtn.disabled = false;
        return;
    }

    if (kembalian < 0) {
        kurangBox.classList.remove('hidden');
        submitBtn.disabled = true;
    } else {
        kembalianBox.classList.remove('hidden');
        kembalianVal.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        submitBtn.disabled = false;
    }
}
</script>
@endsection