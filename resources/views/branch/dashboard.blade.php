@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2 py-1 rounded-full uppercase tracking-wider border; }
    .status-booking_created { @apply bg-slate-800 text-slate-300 border-slate-700; }
    .status-waiting_dropoff { @apply bg-yellow-950/60 text-yellow-400 border-yellow-800/50; }
    .status-weighed { @apply bg-blue-950/60 text-blue-400 border-blue-800/50; }
    .status-payment_pending { @apply bg-orange-950/60 text-orange-400 border-orange-800/50; }
    .status-received_at_branch { @apply bg-indigo-950/60 text-indigo-400 border-indigo-800/50; }
    .status-assigned_to_courier { @apply bg-violet-950/60 text-violet-400 border-violet-800/50; }
    .status-out_for_delivery { @apply bg-cyan-950/60 text-cyan-400 border-cyan-800/50; }
    .status-delivered { @apply bg-emerald-950/60 text-emerald-400 border-emerald-800/50; }
    .status-gagal_kirim { @apply bg-red-950/60 text-red-400 border-red-800/50; }
</style>
@endsection

@section('content')
{{-- Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white">Admin Panel — {{ $branch->name }}</h1>
        <p class="text-sm text-slate-400 mt-1">Kota: <span class="text-slate-200">{{ $branch->city }}</span> &bull; {{ $branch->address }}</p>
    </div>
    <a href="{{ route('branch.scan.show') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5A7.5 7.5 0 117.5 9 7.5 7.5 0 0121 15.5z"/></svg>
        Scan / Cari Paket
    </a>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-8">
    @php
    $statItems = [
        ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate'],
        ['label' => 'Menunggu', 'value' => $stats['waiting_dropoff'], 'color' => 'yellow'],
        ['label' => 'Ditimbang', 'value' => $stats['weighed'], 'color' => 'blue'],
        ['label' => 'Diterima', 'value' => $stats['received'], 'color' => 'indigo'],
        ['label' => 'Ditugaskan', 'value' => $stats['assigned'], 'color' => 'violet'],
        ['label' => 'Transit', 'value' => $stats['transit'], 'color' => 'cyan'],
        ['label' => 'Terkirim', 'value' => $stats['delivered'], 'color' => 'emerald'],
    ];
    $colorMap = ['slate'=>'border-slate-800 text-slate-300','yellow'=>'border-yellow-900/40 text-yellow-400','blue'=>'border-blue-900/40 text-blue-400','indigo'=>'border-indigo-900/40 text-indigo-400','violet'=>'border-violet-900/40 text-violet-400','cyan'=>'border-cyan-900/40 text-cyan-400','emerald'=>'border-emerald-900/40 text-emerald-400'];
    @endphp
    @foreach($statItems as $stat)
    <div class="glass-panel rounded-xl p-4 border {{ $colorMap[$stat['color']] }} text-center">
        <p class="text-2xl font-bold {{ explode(' ', $colorMap[$stat['color']])[1] }}">{{ $stat['value'] }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Shipments Table --}}
<div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <h2 class="text-base font-semibold text-white">Daftar Paket</h2>
        <span class="text-xs text-slate-500">{{ $stats['total'] }} paket</span>
    </div>

    @if($shipments->isEmpty())
        <div class="py-16 text-center">
            <p class="text-slate-500">Belum ada paket di cabang ini.</p>
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
                            <div class="font-mono text-blue-400 text-xs font-semibold">{{ $shipment->tracking_number }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $shipment->booking_code }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-slate-200 text-sm">{{ $shipment->customer->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $shipment->sender_name }}</div>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-300">
                            {{ $shipment->origin_city }} &rarr; {{ $shipment->destination_city }}
                            <div class="text-slate-500 mt-0.5 uppercase">{{ $shipment->service_type }}</div>
                        </td>
                        <td class="px-5 py-4 text-right text-sm">
                            <span class="text-white font-medium">{{ $shipment->actual_weight ? number_format($shipment->actual_weight,1).'kg' : '-' }}</span>
                            <div class="text-xs text-slate-500">est. {{ number_format($shipment->estimated_weight,1) }}kg</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-semibold text-white text-sm">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</span>
                            @if($shipment->payment)
                                <div class="text-xs mt-0.5 {{ $shipment->payment->payment_status === 'paid' ? 'text-emerald-500' : 'text-red-400' }}">
                                    {{ $shipment->payment->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="status-badge status-{{ $shipment->status }}">
                                {{ str_replace('_',' ',$shipment->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                {{-- Confirm Cash --}}
                                @if($shipment->payment && $shipment->payment->payment_status !== 'paid' && in_array($shipment->status, ['weighed','booking_created','waiting_dropoff']))
                                <form method="POST" action="{{ route('branch.confirm-cash', $shipment) }}" onsubmit="return confirm('Konfirmasi pembayaran tunai?')">
                                    @csrf
                                    <button class="text-xs bg-emerald-700/70 hover:bg-emerald-700 text-emerald-100 px-2.5 py-1.5 rounded transition">
                                        Konfirmasi Cash
                                    </button>
                                </form>
                                @endif

                                {{-- Assign Courier --}}
                                @if($shipment->status === 'received_at_branch')
                                <button onclick="openAssignModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}')"
                                        class="text-xs bg-violet-700/70 hover:bg-violet-700 text-violet-100 px-2.5 py-1.5 rounded transition">
                                    Tugaskan Kurir
                                </button>
                                @endif

                                {{-- Scan/Process --}}
                                @if(in_array($shipment->status, ['waiting_dropoff']))
                                <a href="{{ route('branch.scan.show') }}" class="text-xs bg-blue-800/60 hover:bg-blue-700 text-blue-200 px-2.5 py-1.5 rounded transition">
                                    Proses
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

{{-- Assign Courier Modal --}}
<div id="assign-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeAssignModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="glass-panel w-full max-w-sm rounded-2xl border border-slate-700 p-6 relative z-10 shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-1">Tugaskan Kurir</h3>
            <p id="assign-tracking" class="text-xs font-mono text-slate-400 mb-5"></p>
            <form id="assign-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Kurir</label>
                    <select name="courier_id" required class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700 text-white focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition">
                    Tugaskan
                </button>
            </form>
            <button onclick="closeAssignModal()" class="w-full mt-3 py-2 text-sm text-slate-400 hover:text-white transition">Batal</button>
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
</script>
@endsection
