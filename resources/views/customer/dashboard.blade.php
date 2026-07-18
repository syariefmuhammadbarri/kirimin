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
    .card-hover { transition: border-color 0.2s, box-shadow 0.2s; }
    .card-hover:hover { border-color: rgba(59, 130, 246, 0.3); box-shadow: 0 0 20px rgba(59, 130, 246, 0.05); }
</style>
@endsection

@section('content')
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

{{-- Stats Cards --}}
@php
    $totalShipments = $shipments->count();
    $pendingPayment = $shipments->whereIn('status', ['booking_created', 'payment_pending'])->count();
    $inProgress = $shipments->whereIn('status', ['waiting_dropoff', 'pickup_scheduled', 'pickup_assigned', 'picked_up_from_customer', 'weighed', 'received_at_branch', 'assigned_to_courier', 'out_for_delivery'])->count();
    $delivered = $shipments->where('status', 'delivered')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="glass-panel rounded-xl p-5 border border-slate-800">
        <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Total Pengiriman</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalShipments }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-slate-200">
        <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Menunggu Bayar</p>
        <p class="text-3xl font-bold text-slate-700">{{ $pendingPayment }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-slate-200">
        <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Dalam Proses</p>
        <p class="text-3xl font-bold text-slate-700">{{ $inProgress }}</p>
    </div>
    <div class="glass-panel rounded-xl p-5 border border-slate-200">
        <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Terkirim</p>
        <p class="text-3xl font-bold text-slate-700">{{ $delivered }}</p>
    </div>
</div>

{{-- Shipments Table --}}
<div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">Riwayat Pengiriman</h2>
        <span class="text-xs text-slate-600">{{ $totalShipments }} paket</span>
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
                                @if($shipment->payment && $shipment->payment->payment_status !== 'paid')
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
                <p class="text-sm text-slate-700">Pilih metode simulasi pembayaran:</p>
                <div class="glass-panel rounded-lg border border-slate-200 p-4 text-center bg-slate-50">
                    <p class="text-xs text-slate-600 mb-1">Midtrans Payment Gateway</p>
                    <p class="text-sm text-slate-700">Di produksi nyata, widget Midtrans Snap akan tampil di sini.</p>
                </div>
            </div>

            <form id="mock-payment-form" method="POST" action="">
                @csrf
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg transition">
                    Simulasi Pembayaran Berhasil (Demo)
                </button>
            </form>
            <button onclick="closePaymentModal()" class="w-full mt-3 py-2.5 text-sm text-slate-600 hover:text-slate-800 transition">
                Batal
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openPaymentModal(shipmentId, trackingNumber) {
    document.getElementById('modal-tracking').textContent = 'Resi: ' + trackingNumber;
    document.getElementById('mock-payment-form').action = '/customer/payment/mock-settle/' + shipmentId;
    document.getElementById('payment-modal').classList.remove('hidden');
}
function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}
</script>
@endsection
