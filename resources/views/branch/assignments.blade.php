@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2.5 py-1 rounded-full tracking-wide; }
    .status-pending { @apply bg-slate-100 text-slate-700 border border-slate-200; }
    .status-assigned { @apply bg-slate-100 text-slate-700 border border-slate-200; }
    .status-completed { @apply bg-slate-100 text-slate-700 border border-slate-200; }
    .status-cancelled { @apply bg-slate-100 text-slate-700 border border-slate-200; }
    /* Modal overlay */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: white;
        border-radius: 1rem;
        max-width: 480px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Penugasan Kurir</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $branch->name }} &bull; {{ $branch->city }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">← Kembali ke Dashboard</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Tugas</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-slate-800">{{ $stats['active'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Aktif</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-slate-800">{{ $stats['completed'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Selesai</p>
        </div>
        <div class="card-panel rounded-xl p-5">
            <p class="text-2xl font-bold text-slate-800">{{ $stats['cancelled'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Dibatalkan</p>
        </div>
    </div>

    <!-- Shipments Needing Assignment -->
    @if($shipments->isNotEmpty())
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            Paket Membutuhkan Kurir ({{ $shipments->count() }})
        </h2>
        @foreach($shipments as $shipment)
        <div class="card-panel rounded-2xl p-6 border border-blue-200/50 hover:border-blue-400/50 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono text-xs font-semibold text-blue-600">{{ $shipment->tracking_number }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                            @if($shipment->fulfillment_type === 'pickup') bg-amber-50 text-amber-700 border border-amber-200
                            @else bg-indigo-50 text-indigo-700 border border-indigo-200 @endif">
                            {{ strtoupper($shipment->fulfillment_type) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $shipment->receiver_name }} → {{ $shipment->destination_city }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border
                    @if($shipment->fulfillment_type === 'pickup') bg-amber-50 text-amber-700 border-amber-200
                    @else bg-indigo-50 text-indigo-700 border-indigo-200 @endif">
                    {{ $shipment->fulfillment_type === 'pickup' ? 'Jemput' : 'Antar' }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 text-sm text-slate-600">
                <div>
                    <span class="text-xs text-slate-400">Pengirim</span>
                    <p class="font-medium text-slate-700">{{ $shipment->sender_name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Penerima</span>
                    <p class="font-medium text-slate-700">{{ $shipment->receiver_name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Status</span>
                    <p class="font-medium">{{ str_replace('_', ' ', $shipment->status) }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Berat / Tarif</span>
                    <p class="font-medium">{{ $shipment->actual_weight ?? $shipment->estimated_weight }} kg / Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="border-t border-black/5 pt-4">
                <button type="button" onclick="openAssignModal({{ $shipment->id }}, '{{ $shipment->tracking_number }}', '{{ $shipment->fulfillment_type }}', '{{ $shipment->receiver_name }}', '{{ $shipment->destination_city }}')"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    Tugaskan Kurir
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card-panel rounded-2xl p-14 text-center border border-black/5">
        <svg class="w-14 h-14 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-slate-500 font-medium">Tidak ada paket yang memerlukan penugasan kurir saat ini</p>
        <p class="text-slate-400 text-sm mt-1">Tunggu paket masuk ke cabang atau booking pickup dari customer.</p>
    </div>
    @endif

    <!-- Assignments Table -->
    @if($assignments->isNotEmpty())
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Riwayat Penugasan</h2>
        <div class="card-panel rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-black/5 bg-slate-50/50">
                            <th class="text-left font-semibold text-slate-600 p-4">Paket</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Tipe</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Kurir</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Ditugaskan Oleh</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Tanggal</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Status</th>
                            <th class="text-left font-semibold text-slate-600 p-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignments as $assignment)
                            <tr class="border-b border-black/5 hover:bg-slate-50/50 transition">
                                <td class="p-4">
                                    <div class="font-medium text-slate-900">{{ $assignment->shipment->tracking_number ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $assignment->shipment->receiver_name ?? '-' }}</div>
                                </td>
                                <td class="p-4">
                                    @if($assignment->type)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                        @if($assignment->type === 'pickup') bg-amber-50 text-amber-700 border border-amber-200
                                        @else bg-indigo-50 text-indigo-700 border border-indigo-200 @endif">
                                        {{ $assignment->type === 'pickup' ? 'Jemput' : 'Antar' }}
                                    </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-900">{{ $assignment->courier->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $assignment->courier->email }}</div>
                                </td>
                                <td class="p-4 text-slate-600">{{ $assignment->assignor->name }}</td>
                                <td class="p-4 text-slate-600">
                                    @if ($assignment->assigned_at)
                                        {{ $assignment->assigned_at->format('d M Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="status-badge status-{{ $assignment->status }}">{{ $assignment->status }}</span>
                                </td>
                                <td class="p-4 text-slate-500 text-xs max-w-[150px] truncate">{{ $assignment->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-400">
                                    <p class="text-sm font-medium">Belum ada riwayat penugasan kurir</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Courier List -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Kurir Tersedia</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($availableCouriers as $courier)
                <div class="card-panel rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
                                <span class="text-sm font-bold text-slate-700">{{ substr($courier->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $courier->name }}</p>
                                <p class="text-xs text-slate-500">{{ $courier->email }}</p>
                            </div>
                        </div>
                        @php
                            $activeJobs = $assignments->where('courier_id', $courier->id)->whereIn('status', ['pending', 'assigned'])->count();
                        @endphp
                        <span class="text-xs font-semibold {{ $activeJobs >= 5 ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $activeJobs }}/5 aktif
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-slate-600 h-1.5 rounded-full" style="width: {{ min(($activeJobs / 5) * 100, 100) }}%"></div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-400 py-8">
                    <p>Tidak ada kurir aktif terdaftar di cabang ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Popup Assign Courier -->
<div id="assignModal" class="modal-overlay" onclick="if(event.target===this)closeAssignModal()">
    <div class="modal-content p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-slate-900">Tugaskan Kurir</h3>
            <button type="button" onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Shipment Info -->
        <div id="modalShipmentInfo" class="bg-slate-50 rounded-xl p-4 mb-5 space-y-2 text-sm">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-semibold text-blue-600" id="modalTrackingNumber"></span>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" id="modalFulfillmentBadge"></span>
            </div>
            <p class="text-xs text-slate-500">Penerima: <span id="modalReceiverName" class="text-slate-700 font-medium"></span></p>
            <p class="text-xs text-slate-500">Tujuan: <span id="modalDestination" class="text-slate-700 font-medium"></span></p>
        </div>

        <form id="assignForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-semibold text-slate-700 mb-2 block">Pilih Kurir Cabang <span class="text-red-500">*</span></label>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-2">
                        @forelse($availableCouriers as $courier)
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 cursor-pointer transition courier-option" data-id="{{ $courier->id }}">
                            <input type="radio" name="courier_id" value="{{ $courier->id }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500" required>
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-slate-700">{{ substr($courier->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 text-sm">{{ $courier->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $courier->email }}</p>
                                </div>
                            </div>
                            @php
                                $activeJobs = $assignments->where('courier_id', $courier->id)->whereIn('status', ['pending', 'assigned'])->count();
                            @endphp
                            <span class="text-xs font-semibold {{ $activeJobs >= 5 ? 'text-red-600' : 'text-slate-500' }} whitespace-nowrap">
                                {{ $activeJobs }}/5
                            </span>
                        </label>
                        @empty
                        <p class="text-center text-slate-400 py-4 text-sm">Tidak ada kurir aktif tersedia</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700 mb-2 block">Catatan (opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan untuk kurir..."
                           class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAssignModal()"
                            class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        Tugaskan Kurir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAssignModal(shipmentId, trackingNumber, fulfillmentType, receiverName, destination) {
    // Set shipment info
    document.getElementById('modalTrackingNumber').textContent = trackingNumber;
    document.getElementById('modalReceiverName').textContent = receiverName;
    document.getElementById('modalDestination').textContent = destination;

    // Set badge
    const badge = document.getElementById('modalFulfillmentBadge');
    const isPickup = fulfillmentType === 'pickup';
    badge.textContent = isPickup ? 'JEMPUT' : 'ANTAR';
    badge.className = 'text-xs px-2 py-0.5 rounded-full font-semibold ' +
        (isPickup ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200');

    // Set form action
    let assignUrl = '{{ route("branch.assign-courier", ":id") }}';
    document.getElementById('assignForm').action = assignUrl.replace(':id', shipmentId);

    // Show modal
    document.getElementById('assignModal').classList.add('active');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.remove('active');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAssignModal();
});
</script>
@endsection