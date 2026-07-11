@extends('layouts.app')

@section('styles')
<style>
    .stat-card { @apply glass-panel rounded-xl p-5 border text-center; }
    .shipment-card { @apply glass-panel rounded-xl border border-slate-800 p-5; }
    .shipment-card:hover { @apply border-slate-700; transition: border-color 0.2s; }
</style>
@endsection

@section('content')
{{-- Header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-white">Panel Kurir</h1>
    <p class="text-sm text-slate-400 mt-1">Selamat datang, <span class="text-blue-400 font-medium">{{ Auth::user()->name }}</span></p>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card border-slate-800">
        <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Total Tugas</p>
    </div>
    <div class="stat-card border-violet-900/40">
        <p class="text-3xl font-bold text-violet-400">{{ $stats['assigned'] }}</p>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Ditugaskan</p>
    </div>
    <div class="stat-card border-cyan-900/40">
        <p class="text-3xl font-bold text-cyan-400">{{ $stats['transit'] }}</p>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Sedang Antar</p>
    </div>
    <div class="stat-card border-emerald-900/40">
        <p class="text-3xl font-bold text-emerald-400">{{ $stats['delivered'] }}</p>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Terkirim</p>
    </div>
</div>

{{-- Active / Pending Deliveries --}}
@php
$activeShipments = $shipments->whereIn('status', ['assigned_to_courier', 'out_for_delivery']);
$doneShipments = $shipments->whereIn('status', ['delivered', 'gagal_kirim']);
@endphp

@if($activeShipments->isNotEmpty())
<div class="mb-8">
    <h2 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
        Tugas Aktif ({{ $activeShipments->count() }})
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($activeShipments as $shipment)
        <div class="shipment-card {{ $shipment->status === 'out_for_delivery' ? 'border-cyan-900/40' : 'border-violet-900/40' }}"
             x-data="{ showDeliverForm: false, showFailForm: false }">
            {{-- Card Header --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="font-mono text-xs font-semibold text-blue-400">{{ $shipment->tracking_number }}</span>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $shipment->created_at->format('d M Y') }}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full uppercase border
                    {{ $shipment->status === 'out_for_delivery' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' : 'bg-violet-950/60 text-violet-400 border-violet-800/50' }}">
                    {{ str_replace('_',' ', $shipment->status) }}
                </span>
            </div>

            {{-- Route & Receiver --}}
            <div class="mb-4 space-y-2 text-sm">
                <div class="flex items-center gap-2 text-slate-300">
                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $shipment->receiver_name }} &bull; {{ $shipment->receiver_phone }}
                </div>
                <div class="flex items-start gap-2 text-slate-400 text-xs">
                    <svg class="w-4 h-4 mt-0.5 text-slate-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ $shipment->receiver_address }}
                </div>
                <div class="text-xs text-slate-500">
                    {{ $shipment->origin_city }} → <span class="text-slate-300">{{ $shipment->destination_city }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="border-t border-slate-800 pt-4">
                @if($shipment->status === 'assigned_to_courier')
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('courier.out-for-delivery', $shipment) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-xs bg-cyan-700/70 hover:bg-cyan-700 text-cyan-100 font-semibold rounded-lg transition">
                            🚚 Mulai Antar
                        </button>
                    </form>
                </div>
                @endif

                @if($shipment->status === 'out_for_delivery')
                <div class="flex gap-2">
                    <button @click="showDeliverForm = !showDeliverForm; showFailForm = false"
                            class="flex-1 py-2.5 text-xs bg-emerald-700/70 hover:bg-emerald-700 text-emerald-100 font-semibold rounded-lg transition">
                        ✓ Tandai Terkirim
                    </button>
                    <button @click="showFailForm = !showFailForm; showDeliverForm = false"
                            class="flex-1 py-2.5 text-xs bg-red-900/60 hover:bg-red-900 text-red-300 font-semibold rounded-lg transition">
                        ✗ Gagal Kirim
                    </button>
                </div>

                {{-- Deliver Form --}}
                <div x-show="showDeliverForm" x-transition class="mt-4 p-4 bg-slate-900/60 rounded-xl border border-emerald-900/30 space-y-3">
                    <p class="text-xs font-semibold text-emerald-400 mb-2">Bukti Pengiriman</p>
                    <form method="POST" action="{{ route('courier.deliver', $shipment) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-slate-400 mb-1 block">Nama Penerima *</label>
                                <input type="text" name="recipient_name" required
                                       class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 mb-1 block">Foto Bukti (1-3 foto) *</label>
                                <input type="file" name="photos[]" accept="image/*" multiple required
                                       class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-slate-700 file:text-slate-200 file:cursor-pointer hover:file:bg-slate-600">
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 mb-1 block">Tanda Tangan Penerima *</label>
                                <canvas id="sig-{{ $shipment->id }}" width="300" height="80"
                                        class="w-full bg-slate-800 border border-slate-700 rounded-lg cursor-crosshair"
                                        style="touch-action:none"></canvas>
                                <input type="hidden" name="recipient_signature" id="sig-data-{{ $shipment->id }}">
                                <button type="button" onclick="clearCanvas('{{ $shipment->id }}')"
                                        class="mt-1 text-xs text-red-400 hover:text-red-300">Hapus TTD</button>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 mb-1 block">Catatan (opsional)</label>
                                <input type="text" name="notes"
                                       class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                            <button type="submit" onclick="captureSignature('{{ $shipment->id }}')"
                                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg transition">
                                Konfirmasi Terkirim
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Fail Form --}}
                <div x-show="showFailForm" x-transition class="mt-4 p-4 bg-slate-900/60 rounded-xl border border-red-900/30 space-y-3">
                    <p class="text-xs font-semibold text-red-400 mb-2">Alasan Gagal Kirim</p>
                    <form method="POST" action="{{ route('courier.fail', $shipment) }}">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-slate-400 mb-1 block">Alasan *</label>
                                <select name="reason" required
                                        class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-red-500 outline-none">
                                    <option value="">-- Pilih Alasan --</option>
                                    <option>Penerima tidak berada di tempat</option>
                                    <option>Alamat tidak ditemukan</option>
                                    <option>Penerima menolak paket</option>
                                    <option>Nomor telepon tidak aktif</option>
                                    <option>Paket rusak dalam pengiriman</option>
                                </select>
                            </div>
                            <button type="submit"
                                    class="w-full py-2.5 bg-red-700/80 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                Laporkan Gagal Kirim
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="glass-panel rounded-2xl border border-slate-800 py-14 text-center mb-8">
    <svg class="w-14 h-14 text-slate-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <p class="text-slate-400 font-medium">Tidak ada tugas aktif saat ini</p>
    <p class="text-slate-600 text-sm mt-1">Tunggu penugasan dari admin cabang</p>
</div>
@endif

{{-- Completed Deliveries History --}}
@if($doneShipments->isNotEmpty())
<div>
    <h2 class="text-base font-semibold text-slate-400 mb-4">Riwayat Pengiriman ({{ $doneShipments->count() }})</h2>
    <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800">
                    <th class="px-5 py-3 text-left">Resi</th>
                    <th class="px-5 py-3 text-left">Penerima</th>
                    <th class="px-5 py-3 text-left">Tujuan</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($doneShipments as $shipment)
                <tr class="hover:bg-slate-800/20 transition">
                    <td class="px-5 py-3 font-mono text-xs text-blue-400">{{ $shipment->tracking_number }}</td>
                    <td class="px-5 py-3 text-slate-300">{{ $shipment->receiver_name }}</td>
                    <td class="px-5 py-3 text-slate-400 text-xs">{{ $shipment->destination_city }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full uppercase border
                            {{ $shipment->status === 'delivered' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : 'bg-red-950/60 text-red-400 border-red-800/50' }}">
                            {{ $shipment->status === 'delivered' ? 'Terkirim' : 'Gagal' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
// Simple canvas signature
(function() {
    document.querySelectorAll('canvas[id^="sig-"]').forEach(function(canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;
        ctx.strokeStyle = '#60a5fa';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: (clientX - rect.left) * (canvas.width / rect.width),
                     y: (clientY - rect.top) * (canvas.height / rect.height) };
        }
        canvas.addEventListener('mousedown', e => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('touchend', () => drawing = false);
    });
})();

function clearCanvas(id) {
    const canvas = document.getElementById('sig-' + id);
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
}

function captureSignature(id) {
    const canvas = document.getElementById('sig-' + id);
    document.getElementById('sig-data-' + id).value = canvas.toDataURL('image/png');
}
</script>
@endsection
