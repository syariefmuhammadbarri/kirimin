@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wider border; }
    .status-booking_created { @apply bg-slate-100 text-slate-700 border-slate-200; }
    .status-waiting_dropoff { @apply bg-yellow-50 text-yellow-700 border-yellow-200; }
    .status-pickup_scheduled { @apply bg-amber-50 text-amber-700 border-amber-200; }
    .status-pickup_assigned { @apply bg-violet-50 text-violet-700 border-violet-200; }
    .status-picked_up_from_customer { @apply bg-blue-50 text-blue-700 border-blue-200; }
    .status-weighed { @apply bg-blue-50 text-blue-700 border-blue-200; }
    .status-payment_pending { @apply bg-orange-50 text-orange-700 border-orange-200; }
    .status-received_at_branch { @apply bg-indigo-50 text-indigo-700 border-indigo-200; }
    .status-in_transit { @apply bg-sky-50 text-sky-700 border-sky-200; }
    .status-assigned_to_courier { @apply bg-violet-50 text-violet-700 border-violet-200; }
    .status-picked_up { @apply bg-indigo-50 text-indigo-700 border-indigo-200; }
    .status-out_for_delivery { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
    .status-delivery_confirmation_pending { @apply bg-amber-100 text-amber-800 border-amber-300 font-bold animate-pulse; }
    .status-accepted { @apply bg-emerald-100 text-emerald-800 border-emerald-300 font-bold; }
    .status-delivered { @apply bg-emerald-100 text-emerald-800 border-emerald-300 font-bold; }
    .status-gagal_kirim { @apply bg-red-50 text-red-700 border-red-200; }
</style>
@endsection

@section('content')
{{-- Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Admin Panel — {{ $branch->name }}</h1>
        <p class="text-sm text-slate-600 mt-1">Kota: <span class="text-slate-700">{{ $branch->city }}</span> &bull; {{ $branch->address }}</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('branch.delivery-confirmations') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow transition relative">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Konfirmasi Delivery
            @if(($stats['delivery_confirmation'] ?? 0) > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-bounce">{{ $stats['delivery_confirmation'] }}</span>
            @endif
        </a>
        <a href="{{ route('branch.scan.show') }}"
           class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-lg shadow-slate-900/20 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5A7.5 7.5 0 117.5 9 7.5 7.5 0 0121 15.5z"/></svg>
            Scan / Cari Paket
        </a>
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-11 gap-3 mb-8">
    @php
    $statItems = [
        ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate', 'status' => ''],
        ['label' => 'Jemput (Scheduled)', 'value' => $stats['pickup_scheduled'], 'color' => 'yellow', 'status' => 'pickup_scheduled'],
        ['label' => 'Jemput (Assigned)', 'value' => $stats['pickup_assigned'], 'color' => 'violet', 'status' => 'pickup_assigned'],
        ['label' => 'Waiting Dropoff', 'value' => $stats['waiting_dropoff'], 'color' => 'yellow', 'status' => 'waiting_dropoff'],
        ['label' => 'Ditimbang', 'value' => $stats['weighed'], 'color' => 'blue', 'status' => 'weighed'],
        ['label' => 'Diterima Cabang', 'value' => $stats['received'], 'color' => 'indigo', 'status' => 'received_at_branch'],
        ['label' => 'Assign Delivery', 'value' => $stats['assigned'], 'color' => 'violet', 'status' => 'assigned_to_courier'],
        ['label' => 'Transit/Antar', 'value' => $stats['transit'], 'color' => 'cyan', 'status' => 'in_transit'],
        ['label' => 'Verifikasi Antar', 'value' => $stats['delivery_confirmation'] ?? 0, 'color' => 'amber', 'status' => 'delivery_confirmation_pending'],
        ['label' => 'Transit Masuk', 'value' => $stats['transit_in'] ?? 0, 'color' => 'sky', 'status' => 'in_transit'],
        ['label' => 'ACCEPTED (TERKIRIM)', 'value' => $stats['delivered'], 'color' => 'emerald', 'status' => 'delivered'],
    ];
    $colorMap = [
        'slate' => 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50',
        'yellow' => 'border-yellow-200 text-yellow-700 bg-yellow-50/20 hover:bg-yellow-50/40',
        'blue' => 'border-blue-200 text-blue-700 bg-blue-50/20 hover:bg-blue-50/40',
        'indigo' => 'border-indigo-200 text-indigo-700 bg-indigo-50/20 hover:bg-indigo-50/40',
        'violet' => 'border-violet-200 text-violet-700 bg-violet-50/20 hover:bg-violet-50/40',
        'cyan' => 'border-cyan-200 text-cyan-700 bg-cyan-50/20 hover:bg-cyan-50/40',
        'amber' => 'border-amber-300 text-amber-800 bg-amber-50 hover:bg-amber-100',
        'sky' => 'border-sky-200 text-sky-700 bg-sky-50/20 hover:bg-sky-50/40',
        'emerald' => 'border-emerald-200 text-emerald-700 bg-emerald-50/20 hover:bg-emerald-50/40'
    ];
    @endphp
    @foreach($statItems as $stat)
    @php
        $isActive = request('status') === $stat['status'];
    @endphp
    <a href="{{ route('branch.dashboard', $stat['status'] ? ['status' => $stat['status']] : []) }}"
       class="glass-panel rounded-xl p-4 border {{ $colorMap[$stat['color']] }} text-center hover:scale-[1.03] hover:shadow-md transition duration-150 block {{ $isActive ? 'ring-2 ring-blue-600 bg-blue-50/40' : '' }}">
        <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
        <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">{{ $stat['label'] }}</p>
    </a>
    @endforeach
</div>

{{-- Shipments Table --}}
<div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">Daftar Paket</h2>
        <span class="text-xs text-slate-600">{{ $stats['total'] }} paket</span>
    </div>

    @if($shipments->isEmpty())
        <div class="py-16 text-center">
            <p class="text-slate-600">Belum ada paket di cabang ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                        <th class="px-5 py-3 text-left">Resi / Booking</th>
                        <th class="px-5 py-3 text-left">Pelanggan</th>
                        <th class="px-5 py-3 text-left">Rute</th>
                        <th class="px-5 py-3 text-right">Berat</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($shipments as $shipment)
                    <tr class="hover:bg-slate-800/20 transition" id="row-{{ $shipment->id }}">
                        <td class="px-5 py-4">
                            <div class="font-mono text-slate-700 text-xs font-semibold">{{ $shipment->tracking_number }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $shipment->booking_code }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-slate-700 text-sm">{{ $shipment->customer->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $shipment->sender_name }}</div>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-700">
                            {{ $shipment->origin_city }} &rarr; {{ $shipment->destination_city }}
                            <div class="text-slate-500 mt-0.5 uppercase">{{ $shipment->service_type }}</div>
                        </td>
                        <td class="px-5 py-4 text-right text-sm">
                            <span class="text-slate-800 font-medium">{{ $shipment->actual_weight ? number_format($shipment->actual_weight,1).'kg' : '-' }}</span>
                            <div class="text-xs text-slate-500">est. {{ number_format($shipment->estimated_weight,1) }}kg</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-semibold text-slate-800 text-sm">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</span>
                            @if($shipment->payment)
                                <div class="text-xs mt-0.5 {{ $shipment->payment->payment_status === 'paid' ? 'text-slate-700' : 'text-slate-600' }}">
                                    {{ $shipment->payment->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($shipment->status === 'delivered')
                                <span class="status-badge status-accepted">ACCEPTED</span>
                            @else
                                <span class="status-badge status-{{ $shipment->status }}">
                                    {{ str_replace('_',' ',$shipment->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                {{-- Confirm Cash --}}
                                @if($shipment->payment && $shipment->payment->payment_status !== 'paid' && in_array($shipment->status, ['weighed','booking_created','waiting_dropoff']))
                                <form method="POST" action="{{ route('branch.confirm-cash', $shipment) }}" onsubmit="return confirm('Konfirmasi pembayaran tunai?')">
                                    @csrf
                                    <input type="hidden" name="paid_amount" value="{{ $shipment->total_price }}">
                                    <button class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-100 px-2.5 py-1.5 rounded transition">
                                        Konfirmasi Cash
                                    </button>
                                </form>
                                @endif

                                {{-- Assign Pickup Courier --}}
                                @if($shipment->status === 'pickup_scheduled')
                                <button onclick="openPickupModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}', '{{ addslashes($shipment->pickup_address ?? $shipment->sender_address) }}')"
                                        class="text-xs bg-amber-600 hover:bg-amber-500 text-white px-2.5 py-1.5 rounded transition">
                                    🛵 Assign Jemput
                                </button>
                                @endif

                                {{-- Kirim Transit (with next branch select) --}}
                                @if(in_array($shipment->status, ['received_at_branch', 'weighed']) && $shipment->payment && $shipment->payment->payment_status === 'paid' && strtolower($shipment->destination_city) !== strtolower($branch->city))
                                <button onclick="openTransitModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}')"
                                        class="text-xs bg-sky-700 hover:bg-sky-600 text-white px-2.5 py-1.5 rounded transition">
                                    🚚 Kirim Transit
                                </button>
                                @endif

                                {{-- Terima Transit --}}
                                @if($shipment->status === 'in_transit' && $shipment->next_branch_id === $branch->id)
                                <form method="POST" action="{{ route('branch.receive-transit', $shipment) }}" onsubmit="return confirm('Konfirmasi penerimaan paket transit ini?')">
                                    @csrf
                                    <button class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white px-2.5 py-1.5 rounded transition">
                                        ✓ Terima Transit
                                    </button>
                                </form>
                                @endif

                                {{-- Assign Delivery Courier --}}
                                @if($shipment->status === 'received_at_branch' && strtolower($shipment->destination_city) === strtolower($branch->city))
                                <button onclick="openAssignModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}')"
                                        class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-100 px-2.5 py-1.5 rounded transition">
                                    Tugaskan Kurir
                                </button>
                                @endif

                                {{-- Verifikasi Delivery --}}
                                @if($shipment->status === 'delivery_confirmation_pending')
                                <a href="{{ route('branch.delivery-confirmations') }}" class="text-xs bg-amber-600 hover:bg-amber-500 text-white font-semibold px-2.5 py-1.5 rounded transition shadow-sm inline-flex items-center gap-1">
                                    ✓ Verifikasi Bukti
                                </a>
                                @endif

                                {{-- Scan/Process --}}
                                @if(in_array($shipment->status, ['waiting_dropoff', 'picked_up_from_customer']))
                                <a href="{{ route('branch.shipment.process', $shipment) }}" class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-100 px-2.5 py-1.5 rounded transition">
                                    Proses / Timbang
                                </a>
                                @endif

                                {{-- Receipt --}}
                                @if(!in_array($shipment->status, ['booking_created']))
                                <a href="{{ route('branch.receipt', $shipment) }}" class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-2.5 py-1.5 rounded transition">
                                    Resi
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Assign Delivery Courier Modal --}}
<div id="assign-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeAssignModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-sm rounded-2xl border border-slate-700 p-6 relative z-10 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-800 mb-1">Tugaskan Kurir Delivery</h3>
            <p id="assign-tracking" class="text-xs font-mono text-slate-600 mb-5"></p>
            <form id="assign-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Kurir</label>
                    <select name="courier_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:ring-2 focus:ring-slate-500 text-sm">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                    Tugaskan
                </button>
            </form>
            <button onclick="closeAssignModal()" class="w-full mt-3 py-2 text-sm text-slate-600 hover:text-slate-800 transition">Batal</button>
        </div>
    </div>
</div>

{{-- Assign Pickup Courier Modal --}}
<div id="pickup-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closePickupModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-sm rounded-2xl border border-amber-600/40 p-6 relative z-10 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-800 mb-1">🛵 Assign Kurir Penjemputan</h3>
            <p id="pickup-tracking" class="text-xs font-mono text-amber-700 mb-1"></p>
            <p id="pickup-address" class="text-xs text-slate-500 mb-5"></p>
            <form id="pickup-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Kurir</label>
                    <select name="courier_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:ring-2 focus:ring-amber-500 text-sm">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-lg transition">
                    Tugaskan untuk Jemput
                </button>
            </form>
            <button onclick="closePickupModal()" class="w-full mt-3 py-2 text-sm text-slate-600 hover:text-slate-800 transition">Batal</button>
        </div>
    </div>
</div>

{{-- Kirim Transit Modal --}}
<div id="transit-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeTransitModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-sm rounded-2xl border border-sky-600/40 p-6 relative z-10 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-800 mb-1">🚚 Kirim Transit</h3>
            <p id="transit-tracking" class="text-xs font-mono text-sky-700 mb-5"></p>
            <form id="transit-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cabang Tujuan Berikutnya</label>
                    <select name="next_branch_id" required class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:ring-2 focus:ring-sky-500 text-sm">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $b)
                            @if($b->id !== $branch->id)
                            <option value="{{ $b->id }}">{{ $b->name }} — {{ $b->city }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-sky-700 hover:bg-sky-600 text-white text-sm font-semibold rounded-lg transition">
                    Konfirmasi Kirim Transit
                </button>
            </form>
            <button onclick="closeTransitModal()" class="w-full mt-3 py-2 text-sm text-slate-600 hover:text-slate-800 transition">Batal</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAssignModal(id, tracking) {
    document.getElementById('assign-tracking').textContent = 'Resi: ' + tracking;
    document.getElementById('assign-form').action = '/branch/assign-courier/' + id;
    document.getElementById('assign-modal').classList.remove('hidden');
}
function closeAssignModal() {
    document.getElementById('assign-modal').classList.add('hidden');
}
function openPickupModal(id, tracking, address) {
    document.getElementById('pickup-tracking').textContent = 'Resi: ' + tracking;
    document.getElementById('pickup-address').textContent = '📍 ' + address;
    document.getElementById('pickup-form').action = '/branch/assign-pickup-courier/' + id;
    document.getElementById('pickup-modal').classList.remove('hidden');
}
function closePickupModal() {
    document.getElementById('pickup-modal').classList.add('hidden');
}
function openTransitModal(id, tracking) {
    document.getElementById('transit-tracking').textContent = 'Resi: ' + tracking;
    document.getElementById('transit-form').action = '/branch/send-transit/' + id;
    document.getElementById('transit-modal').classList.remove('hidden');
}
function closeTransitModal() {
    document.getElementById('transit-modal').classList.add('hidden');
}
</script>
@endsection
