@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2.5 py-1 rounded-full uppercase tracking-wider; }
    .status-booking_created { @apply bg-slate-800 text-slate-300 border border-slate-700; }
    .status-waiting_dropoff { @apply bg-yellow-950/60 text-yellow-400 border border-yellow-800/50; }
    .status-pickup_scheduled { @apply bg-amber-950/60 text-amber-400 border border-amber-800/50; }
    .status-pickup_assigned { @apply bg-violet-950/60 text-violet-400 border border-violet-800/50; }
    .status-picked_up_from_customer { @apply bg-blue-950/60 text-blue-400 border border-blue-800/50; }
    .status-weighed { @apply bg-blue-950/60 text-blue-400 border border-blue-800/50; }
    .status-payment_pending { @apply bg-orange-950/60 text-orange-400 border border-orange-800/50; }
    .status-received_at_branch { @apply bg-indigo-950/60 text-indigo-400 border border-indigo-800/50; }
    .status-assigned_to_courier { @apply bg-violet-950/60 text-violet-400 border border-violet-800/50; }
    .status-out_for_delivery { @apply bg-cyan-950/60 text-cyan-400 border border-cyan-800/50; }
    .status-delivered { @apply bg-emerald-950/60 text-emerald-400 border border-emerald-800/50; }
    .status-gagal_kirim { @apply bg-red-950/60 text-red-400 border border-red-800/50; }
    .status-cancelled { @apply bg-red-950/60 text-red-300 border border-red-800/50; }
    .status-returned { @apply bg-orange-950/60 text-orange-300 border border-orange-800/50; }
    .card-hover { transition: border-color 0.2s, box-shadow 0.2s; }
    .card-hover:hover { border-color: rgba(59, 130, 246, 0.3); box-shadow: 0 0 20px rgba(59, 130, 246, 0.05); }
</style>
@endsection

@section('content')
@if(session('qr_code') && session('booking_code'))
<div id="booking-success-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="document.getElementById('booking-success-modal').remove()"></div>
    <div class="relative glass-panel w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 z-10 shadow-2xl text-center space-y-4">
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.6" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-800">Booking Berhasil Dibuat!</h3>
            <p class="text-xs text-slate-500 mt-1">{{ session('success') }}</p>
        </div>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col items-center">
            <img src="{{ session('qr_code') }}" alt="QR Code Booking" class="w-48 h-48 bg-white border border-slate-200 p-2 rounded-lg shadow-sm">
            <div class="mt-3">
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Kode Booking</span>
                <span class="font-mono font-bold text-base text-slate-700 block select-all">{{ session('booking_code') }}</span>
            </div>
        </div>
        <div class="text-left text-xs text-slate-600 space-y-1.5 p-3 bg-blue-50/50 rounded-lg border border-blue-100/50">
            <p class="font-semibold text-slate-700">Langkah Selanjutnya:</p>
            <p>1. Tunjukkan QR Code ini kepada petugas outlet saat menyerahkan paket.</p>
            <p>2. Lakukan pembayaran melalui transfer online (Midtrans) atau tunai di cabang.</p>
        </div>
        <button onclick="document.getElementById('booking-success-modal').remove()"
                class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition duration-150">
            Tutup & Buka Dashboard
        </button>
    </div>
</div>
@endif

{{-- Page Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Dashboard Pelanggan</h1>
        <p class="text-sm text-slate-600 mt-1">Selamat datang kembali, <span class="text-slate-700 font-medium">{{ $customer->name ?? Auth::user()->name }}</span></p>
    </div>
    <a href="{{ route('customer.booking.create') }}"
       class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-lg shadow-slate-900/20 transition duration-150">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Pengiriman Baru
    </a>
</div>

{{-- Stats Cards — data akurat dari semua shipment, bukan hanya halaman ini --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="glass-panel rounded-xl p-5 border border-slate-800">
        <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Total Pengiriman</p>
        <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-amber-800/40 bg-amber-950/10">
        <p class="text-xs text-amber-600 uppercase tracking-wider mb-1">Menunggu Bayar</p>
        <p class="text-3xl font-bold text-amber-700">{{ $stats['pending_payment'] }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-blue-800/40 bg-blue-950/10">
        <p class="text-xs text-blue-600 uppercase tracking-wider mb-1">Dalam Proses</p>
        <p class="text-3xl font-bold text-blue-700">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-emerald-800/40 bg-emerald-950/10">
        <p class="text-xs text-emerald-600 uppercase tracking-wider mb-1">Terkirim</p>
        <p class="text-3xl font-bold text-emerald-700">{{ $stats['delivered'] }}</p>
    </div>
</div>

{{-- Shipments Table --}}
<div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">Riwayat Pengiriman</h2>
        <span class="text-xs text-slate-600">{{ $stats['total'] }} paket</span>
    </div>

    @if($shipments->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center px-4">
            <svg class="w-16 h-16 text-slate-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-slate-600 font-medium">Belum ada pengiriman</p>
            <p class="text-slate-500 text-sm mt-1">Buat pengiriman pertama Anda sekarang</p>
            <a href="{{ route('customer.booking.create') }}" class="mt-4 text-sm text-slate-700 hover:text-slate-900 font-medium transition">Buat Pengiriman &rarr;</a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                        <th class="px-6 py-3 text-left font-medium">Nomor Resi</th>
                        <th class="px-6 py-3 text-left font-medium">Rute</th>
                        <th class="px-6 py-3 text-left font-medium">Layanan</th>
                        <th class="px-6 py-3 text-right font-medium">Total</th>
                        <th class="px-6 py-3 text-center font-medium">Status</th>
                        <th class="px-6 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($shipments as $shipment)
                    <tr class="hover:bg-slate-800/20 transition">
                        <td class="px-6 py-4">
                            <div class="font-mono text-slate-700 text-xs font-semibold">{{ $shipment->tracking_number }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $shipment->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-slate-700 font-medium">{{ $shipment->origin_city }}</div>
                            <div class="flex items-center gap-1 text-xs text-slate-600 mt-0.5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                {{ $shipment->destination_city }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold uppercase px-2 py-1 rounded 
                                {{ $shipment->service_type === 'express' ? 'bg-slate-100 text-slate-700 border border-slate-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                {{ $shipment->service_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold text-slate-800">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</span>
                            @if($shipment->payment)
                                <div class="text-xs mt-0.5 {{ $shipment->payment->payment_status === 'paid' ? 'text-slate-700' : 'text-slate-600' }}">
                                    {{ $shipment->payment->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="status-badge status-{{ $shipment->status }}">
                                {{ str_replace('_', ' ', $shipment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Pay button if pending --}}
                                @if($shipment->payment && $shipment->payment->payment_status !== 'paid' && !in_array($shipment->status, ['cancelled', 'returned']))
                                    <button onclick="openPaymentModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}')"
                                            class="text-xs bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded transition font-medium">
                                        Bayar
                                    </button>
                                @endif
                                {{-- Invoice download if paid --}}
                                @if($shipment->payment && $shipment->payment->payment_status === 'paid')
                                    <a href="{{ route('customer.invoice.download', $shipment) }}"
                                       class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-100 px-3 py-1.5 rounded transition font-medium">
                                        Invoice
                                    </a>
                                @endif
                                {{-- Cancel button: FR-01 — hanya jika eligible --}}
                                @if($shipment->isCancellable())
                                    <button onclick="openCancelModal({{ $shipment->id }}, '{{ $shipment->booking_code }}')"
                                            class="text-xs bg-red-950/60 hover:bg-red-900/60 text-red-400 border border-red-800/50 px-3 py-1.5 rounded transition font-medium">
                                        Batalkan
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $shipments->links() }}
        </div>
    @endif
</div>

{{-- Payment Modal --}}
<div id="payment-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-md rounded-2xl border border-slate-700 shadow-2xl p-6 relative z-10">
            <h3 class="text-lg font-bold text-slate-800 mb-1">Pembayaran Pengiriman</h3>
            <p id="modal-tracking" class="text-sm text-slate-600 mb-6 font-mono"></p>

            <div class="space-y-3 mb-6">
                <p class="text-sm text-slate-700">Pilih metode pembayaran:</p>
                <!-- Snap embed - akan tampil saat Snap siap -->
                <div id="snap-container" class="w-full min-h-[100px]"></div>

                <!-- Fallback mock mode -->
                <div id="mock-container" class="w-full">
                    <div class="glass-panel rounded-lg border border-slate-200 p-4 text-center bg-slate-50">
                        <p class="text-xs text-slate-600 mb-1">Midtrans Payment Gateway</p>
                        <p class="text-sm text-slate-700">Klik tombol di bawah untuk simulasi pembayaran.</p>
                    </div>
                    <form id="mock-payment-form" method="POST" action="" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg transition">
                            Simulasi Pembayaran Berhasil (Demo)
                        </button>
                    </form>
                </div>
            </div>

            <button onclick="closePaymentModal()" class="w-full mt-3 py-2.5 text-sm text-slate-600 hover:text-slate-800 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Cancel Confirmation Modal — FR-01 --}}
<div id="cancel-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeCancelModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-md rounded-2xl border border-red-900/40 shadow-2xl p-6 relative z-10">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-950/60 border border-red-800/50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Batalkan Booking?</h3>
                    <p id="cancel-booking-code" class="text-sm text-slate-500 font-mono mt-0.5"></p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-4">Tindakan ini tidak dapat dibatalkan. Booking yang sudah dibatalkan tidak dapat diaktifkan kembali.</p>

            <form id="cancel-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="cancel_reason" class="block text-xs font-medium text-slate-600 mb-1.5">Alasan pembatalan (opsional)</label>
                    <input type="text" name="cancel_reason" id="cancel_reason"
                           placeholder="Contoh: Salah input alamat"
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-500 focus:outline-none focus:border-red-700 transition">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()"
                            class="flex-1 py-2.5 text-sm text-slate-600 hover:text-slate-800 border border-slate-700 rounded-lg transition">
                        Kembali
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-red-700 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition">
                        Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
$midtransClientKey = config('services.midtrans.client_key');
$midtransMockMode = config('services.midtrans.mock_mode', true);
@endphp
<script>
// ====== Midtrans Snap Integration ======
const MIDTRANS_CLIENT_KEY = '{{ $midtransClientKey }}';
const IS_MOCK_MODE = {{ $midtransMockMode ? 'true' : 'false' }};

// Load Snap script hanya jika real mode dan client_key tersedia
if (!IS_MOCK_MODE && MIDTRANS_CLIENT_KEY && MIDTRANS_CLIENT_KEY !== 'SB-Mid-client-your_client_key_here') {
    const snapScript = document.createElement('script');
    snapScript.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
    snapScript.setAttribute('data-client-key', MIDTRANS_CLIENT_KEY);
    document.head.appendChild(snapScript);
}

function openPaymentModal(shipmentId, trackingNumber) {
    document.getElementById('modal-tracking').textContent = 'Resi: ' + trackingNumber;
    document.getElementById('mock-payment-form').action = '/customer/payment/mock-settle/' + shipmentId;

    if (!IS_MOCK_MODE && MIDTRANS_CLIENT_KEY && MIDTRANS_CLIENT_KEY !== 'SB-Mid-client-your_client_key_here') {
        // === REAL MIDTRANS SNAP MODE ===
        document.getElementById('mock-container').style.display = 'none';
        document.getElementById('snap-container').innerHTML = '<div class="text-center py-8"><div class="animate-spin w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full mx-auto mb-3"></div><p class="text-sm text-slate-600">Memuat pembayaran...</p></div>';
        document.getElementById('payment-modal').classList.remove('hidden');

        // Fetch snap token via AJAX
        fetch('/customer/payment/' + shipmentId)
            .then(res => res.json())
            .then(data => {
                if (data.snap_token && data.snap_token !== '' && !data.snap_token.startsWith('mock_')) {
                    window.snapEmbed(data.snap_token, 'snap-container', {
                        onSuccess: function(result) {
                            document.getElementById('snap-container').innerHTML =
                                '<div class="text-center py-6 text-emerald-600"><svg class="w-12 h-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="font-semibold">Pembayaran Berhasil!</p><p class="text-xs text-slate-500 mt-1">Silakan tutup dan refresh halaman.</p></div>';
                            setTimeout(() => location.reload(), 2000);
                        },
                        onPending: function(result) {
                            document.getElementById('snap-container').innerHTML =
                                '<div class="text-center py-6 text-amber-600"><p class="font-semibold">Menunggu Pembayaran</p><p class="text-xs text-slate-500 mt-1">Selesaikan pembayaran Anda di halaman Midtrans.</p></div>';
                        },
                        onError: function(result) {
                            document.getElementById('snap-container').innerHTML =
                                '<div class="text-center py-6 text-red-600"><p class="font-semibold">Gagal Memproses</p><p class="text-xs text-slate-500 mt-1">Silakan coba lagi.</p></div>';
                        },
                        onClose: function() {
                            document.getElementById('snap-container').innerHTML = '';
                        }
                    });
                } else {
                    // Token mock → fallback ke mock container
                    document.getElementById('mock-container').style.display = 'block';
                    document.getElementById('snap-container').innerHTML = '';
                }
            })
            .catch(() => {
                document.getElementById('mock-container').style.display = 'block';
                document.getElementById('snap-container').innerHTML = '';
            });
    } else {
        // === MOCK MODE ===
        document.getElementById('mock-container').style.display = 'block';
        document.getElementById('snap-container').innerHTML = '';
        document.getElementById('payment-modal').classList.remove('hidden');
    }
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
    document.getElementById('snap-container').innerHTML = '';
    document.getElementById('mock-container').style.display = 'block';
}

function openCancelModal(shipmentId, bookingCode) {
    document.getElementById('cancel-booking-code').textContent = 'Booking: ' + bookingCode;
    document.getElementById('cancel-form').action = '/customer/shipment/' + shipmentId + '/cancel';
    document.getElementById('cancel_reason').value = '';
    document.getElementById('cancel-modal').classList.remove('hidden');
}
function closeCancelModal() {
    document.getElementById('cancel-modal').classList.add('hidden');
}
</script>
@endsection
