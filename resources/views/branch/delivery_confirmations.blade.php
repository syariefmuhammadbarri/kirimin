@extends('layouts.app')

@section('styles')
<style>
    .status-badge { @apply text-xs font-semibold px-2.5 py-1 rounded-full tracking-wide border; }
    .status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
    .status-accepted { @apply bg-emerald-50 text-emerald-700 border-emerald-200; }
    .status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.5);
        z-index: 50; display: none; align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white; border-radius: 1rem; max-width: 500px; width: 100%;
        max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Konfirmasi Pengiriman</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $branch->name }} &bull; {{ $branch->city }}</p>
        </div>
        <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">← Kembali ke Dashboard</a>
    </div>

    <!-- Pending Confirmations -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            Menunggu Persetujuan ({{ $pendingConfirmations->count() }})
        </h2>

        @forelse($pendingConfirmations as $proof)
        <div class="card-panel rounded-2xl p-6 border border-amber-200/50">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono text-xs font-semibold text-amber-600">{{ $proof->shipment->tracking_number ?? '-' }}</span>
                        <span class="status-badge status-pending">Pending</span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $proof->created_at->format('d M Y H:i') }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border bg-blue-50 text-blue-700 border-blue-200">{{ $proof->courier->name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 text-sm text-slate-600">
                <div>
                    <span class="text-xs text-slate-400">Pengirim</span>
                    <p class="font-medium text-slate-700">{{ $proof->shipment->sender_name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Penerima</span>
                    <p class="font-medium text-slate-700">{{ $proof->recipient_name }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Rute</span>
                    <p class="font-medium">{{ $proof->shipment->origin_city }} → {{ $proof->shipment->destination_city }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Alamat Penerima</span>
                    <p class="font-medium truncate">{{ $proof->shipment->receiver_address }}</p>
                </div>
            </div>

            @if($proof->notes)
            <div class="mb-4 p-3 bg-slate-50 rounded-lg text-xs text-slate-600">
                <span class="font-semibold">Catatan Kurir:</span> {{ $proof->notes }}
            </div>
            @endif

            @if($proof->photos && count($proof->photos) > 0)
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-600 mb-2">Foto Bukti:</p>
                <div class="flex gap-2">
                    @foreach($proof->photos as $photo)
                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="w-16 h-16 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 hover:opacity-80 transition">
                        <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($proof->recipient_signature)
            <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                <p class="text-xs font-semibold text-slate-600 mb-1">Tanda Tangan Penerima:</p>
                <img src="{{ $proof->recipient_signature }}" class="h-12 bg-white border border-slate-200 rounded">
            </div>
            @endif

            <div class="flex gap-3 pt-3 border-t border-black/5">
                <button onclick="openAcceptModal({{ $proof->id }}, '{{ $proof->shipment->tracking_number }}')"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    ✓ Accept
                </button>
                <button onclick="openRejectModal({{ $proof->id }}, '{{ $proof->shipment->tracking_number }}')"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    ✗ Reject
                </button>
            </div>
        </div>
        @empty
        <div class="card-panel rounded-2xl p-14 text-center border border-black/5">
            <svg class="w-14 h-14 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-slate-500 font-medium">Tidak ada konfirmasi menunggu</p>
            <p class="text-slate-400 text-sm mt-1">Semua konfirmasi sudah diproses.</p>
        </div>
        @endforelse
    </div>

    <!-- Accepted History -->
    @if($acceptedConfirmations->isNotEmpty())
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Riwayat Diterima</h2>
        <div class="card-panel rounded-2xl overflow-hidden border border-black/5">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-black/5 bg-slate-50/50">
                        <th class="text-left font-semibold text-slate-600 p-4">Resi</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Penerima</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Kurir</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Tgl Diterima</th>
                        <th class="text-left font-semibold text-slate-600 p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach($acceptedConfirmations as $proof)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-4 font-mono text-xs text-blue-600">{{ $proof->shipment->tracking_number }}</td>
                        <td class="p-4 text-slate-700">{{ $proof->recipient_name }}</td>
                        <td class="p-4 text-slate-600">{{ $proof->courier->name }}</td>
                        <td class="p-4 text-slate-600">{{ $proof->reviewed_at ? $proof->reviewed_at->format('d M Y H:i') : '-' }}</td>
                        <td class="p-4"><span class="status-badge status-accepted">Accepted</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Accept Modal -->
<div id="acceptModal" class="modal-overlay" onclick="if(event.target===this)closeAcceptModal()">
    <div class="modal-content p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Accept Pengiriman</h3>
        <p class="text-sm text-slate-500 mb-6">Konfirmasi bahwa paket <span id="acceptResi" class="font-mono font-semibold"></span> telah terkirim?</p>
        <form id="acceptForm" method="POST">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="closeAcceptModal()" class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">Ya, Accept</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay" onclick="if(event.target===this)closeRejectModal()">
    <div class="modal-content p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Tolak Konfirmasi</h3>
        <p class="text-sm text-slate-500 mb-4">Masukkan alasan penolakan untuk konfirmasi <span id="rejectResi" class="font-mono font-semibold"></span></p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="space-y-4">
                <textarea name="reject_reason" rows="3" required placeholder="Alasan penolakan..."
                          class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:ring-1 focus:ring-red-500 outline-none"></textarea>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">Tolak</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAcceptModal(id, resi) {
    document.getElementById('acceptResi').textContent = resi;
    document.getElementById('acceptForm').action = '{{ route("branch.delivery-confirmations.accept", "") }}/' + id;
    document.getElementById('acceptModal').classList.add('active');
}
function closeAcceptModal() { document.getElementById('acceptModal').classList.remove('active'); }

function openRejectModal(id, resi) {
    document.getElementById('rejectResi').textContent = resi;
    document.getElementById('rejectForm').action = '{{ route("branch.delivery-confirmations.reject", "") }}/' + id;
    document.getElementById('rejectModal').classList.add('active');
}
function closeRejectModal() { document.getElementById('rejectModal').classList.remove('active'); }

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeAcceptModal(); closeRejectModal(); }
});
</script>
@endsection