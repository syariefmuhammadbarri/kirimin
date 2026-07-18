@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('courier.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-600 flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Tugas
        </a>
    </div>

    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pengiriman</h1>
            <p class="text-sm text-slate-500 mt-1">Nomor Resi: <span class="font-mono font-semibold text-slate-700">{{ $shipment->tracking_number }}</span></p>
        </div>
        <span class="text-xs font-semibold uppercase px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 mt-1">
            {{ str_replace('_', ' ', $shipment->status) }}
        </span>
    </div>

    @if($errors->any())
    <x-alert type="error">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kolom Kiri: Info Paket --}}
        <div class="space-y-5">
            {{-- Identitas Paket --}}
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">📦 Identitas Paket</h2>
                <div class="space-y-3 text-sm">
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
                        <span class="text-slate-500">Kode Booking</span>
                        <span class="font-mono text-slate-600">{{ $shipment->booking_code }}</span>
                    </div>
                </div>
            </div>

            {{-- Pengirim & Penerima --}}
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">👤 Pengirim & Penerima</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Pengirim</p>
                        <p class="text-slate-800 font-medium">{{ $shipment->sender_name }}</p>
                        <p class="text-slate-500 text-xs mt-0.5">{{ $shipment->sender_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $shipment->sender_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Penerima</p>
                        <p class="text-slate-800 font-medium">{{ $shipment->receiver_name }}</p>
                        <p class="text-slate-500 text-xs mt-0.5">{{ $shipment->receiver_phone }}</p>
                        <p class="text-slate-500 text-xs mt-1">{{ $shipment->receiver_address }}</p>
                    </div>
                </div>
            </div>

            {{-- Daftar Barang --}}
            @if($shipment->items->isNotEmpty())
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">📋 Daftar Barang ({{ $shipment->items->count() }} item)</h2>
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

            {{-- Timeline Tracking --}}
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">📋 Riwayat Status</h2>
                @php $trackings = $shipment->trackings()->orderBy('tracked_at', 'desc')->get(); @endphp
                @if($trackings->isNotEmpty())
                <div class="relative pl-6 border-l-2 border-slate-200 space-y-5 ml-3">
                    @foreach($trackings as $tracking)
                    <div class="relative">
                        <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-white
                            @if($loop->first) bg-blue-600 @else bg-slate-300 @endif"></div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                {{ $tracking->tracked_at->format('d M Y H:i:s') }}
                            </span>
                            <div class="text-sm font-semibold text-slate-600">{{ $tracking->location }}</div>
                            <p class="text-xs text-slate-500">{{ $tracking->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400">Belum ada riwayat perpindahan.</p>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Aksi & POD --}}
        <div class="space-y-5">
            {{-- Quick Actions --}}
            @if(!in_array($shipment->status, ['delivered', 'gagal_kirim', 'returned']))
            <div class="glass-panel rounded-2xl border border-blue-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">⚡ Aksi Cepat</h2>
                <div class="space-y-3">
                    @if(in_array($shipment->status, ['assigned_to_courier', 'picked_up']))
                    <form method="POST" action="{{ route('courier.out-for-delivery', $shipment) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                            🚚 Mulai Pengantaran (Out for Delivery)
                        </button>
                    </form>
                    @endif

                    @if(in_array($shipment->status, ['pickup_assigned']))
                    <form method="POST" action="{{ route('courier.collect', $shipment) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                            📦 Konfirmasi Jemput Paket dari Customer
                        </button>
                    </form>
                    @endif

                    @if($shipment->status === 'picked_up_from_customer')
                    <form method="POST" action="{{ route('courier.drop-at-branch', $shipment) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                            🏢 Serahkan Paket ke Cabang
                        </button>
                    </form>
                    @endif

                    @if($shipment->status === 'out_for_delivery')
                    <form method="POST" action="{{ route('courier.fail', $shipment) }}" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="reason" required placeholder="Alasan gagal kirim..."
                                   class="w-full px-3 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                            ❌ Gagal Kirim
                        </button>
                    </form>
                    @endif

                    @if($shipment->status === 'gagal_kirim')
                    <form method="POST" action="{{ route('courier.retry', $shipment) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                            🔄 Coba Kirim Ulang
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

            {{-- POD: Proof of Delivery --}}
            @if(in_array($shipment->status, ['out_for_delivery']))
            <div class="glass-panel rounded-2xl border border-emerald-200 bg-white p-6">
                <h2 class="text-base font-semibold text-slate-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Bukti Pengiriman (POD)
                </h2>
                <p class="text-sm text-slate-500 mb-5">Upload foto bukti pengiriman, tanda tangan digital penerima, dan selesaikan pengantaran.</p>

                <form method="POST" action="{{ route('courier.shipment.complete', $shipment) }}" enctype="multipart/form-data" id="pod-form">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2" for="recipient_name">
                            Nama Penerima <span class="text-red-500">*</span>
                        </label>
                        <input id="recipient_name" type="text" name="recipient_name" required
                               placeholder="Nama lengkap penerima paket"
                               class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Foto Bukti Pengiriman <span class="text-red-500">*</span>
                            <span class="text-xs text-slate-400 font-normal">(min 1, max 3 foto)</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3" id="photo-preview-container">
                            @for($i = 0; $i < 3; $i++)
                            <label class="relative flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl p-4 cursor-pointer hover:border-emerald-400 transition bg-slate-50" id="photo-label-{{ $i }}">
                                <input type="file" name="photos[]" accept="image/jpeg,image/png,image/jpg"
                                       class="absolute inset-0 opacity-0 cursor-pointer photo-input"
                                       onchange="previewPhoto(this, {{ $i }})"
                                       {{ $i === 0 ? 'required' : '' }}>
                                <svg class="w-6 h-6 text-slate-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                <span class="text-xs text-slate-400">Foto {{ $i + 1 }}</span>
                                <img id="photo-preview-{{ $i }}" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl" alt="Preview">
                            </label>
                            @endfor
                        </div>
                        @error('photos')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        @error('photos.*')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanda Tangan Digital Penerima <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-2">
                            <canvas id="signature-canvas" width="400" height="200"
                                    class="w-full border border-slate-200 rounded-lg bg-white cursor-crosshair touch-none"></canvas>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-slate-400">Silakan minta penerima tanda tangan di area di atas.</p>
                            <button type="button" onclick="clearSignature()"
                                    class="text-xs text-red-500 hover:text-red-700 transition">Hapus</button>
                        </div>
                        <input type="hidden" name="recipient_signature" id="signature-input" required>
                        @error('recipient_signature')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-700 mb-2" for="notes">
                            Catatan (opsional)
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm resize-none"
                                  placeholder="Kondisi paket, lokasi serah terima, dll..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-200">
                        ✓ Kirim Bukti & Selesaikan Pengiriman
                    </button>
                </form>
            </div>
            @elseif(in_array($shipment->status, ['delivered', 'returned']))
            {{-- Completed Status --}}
            <div class="glass-panel rounded-2xl border border-slate-200 p-5 text-center">
                <div class="text-4xl mb-3">✅</div>
                <p class="text-slate-700 font-semibold text-sm">
                    {{ $shipment->status === 'delivered' ? 'Paket Telah Terkirim' : 'Paket Dikembalikan ke Pengirim' }}
                </p>
                <p class="text-xs text-slate-500 mt-1 capitalize">Status: {{ str_replace('_', ' ', $shipment->status) }}</p>
            </div>
            @endif

            {{-- Existing Delivery Proof --}}
            @if($shipment->deliveryProof)
            <div class="glass-panel rounded-2xl border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">🖼️ Bukti Pengiriman Tersimpan</h2>
                <p class="text-sm text-slate-600 mb-2">Diterima oleh: <span class="font-semibold">{{ $shipment->deliveryProof->recipient_name }}</span></p>
                @if(!empty($shipment->deliveryProof->photos))
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach($shipment->deliveryProof->photos as $photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Bukti Foto" class="rounded-lg w-full h-24 object-cover border border-slate-200">
                    @endforeach
                </div>
                @endif
                @if($shipment->deliveryProof->notes)
                <p class="text-xs text-slate-500">Catatan: {{ $shipment->deliveryProof->notes }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Photo preview
function previewPhoto(input, index) {
    const preview = document.getElementById('photo-preview-' + index);
    const label = document.getElementById('photo-label-' + index);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            label.querySelector('svg').classList.add('hidden');
            label.querySelector('span').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Signature Canvas
(function() {
    const canvas = document.getElementById('signature-canvas');
    const ctx = canvas.getContext('2d');
    const hiddenInput = document.getElementById('signature-input');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    // Set canvas resolution
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startDraw(e) {
        e.preventDefault();
        isDrawing = true;
        const pos = getPos(e);
        lastX = pos.x;
        lastY = pos.y;
    }

    function draw(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(pos.x, pos.y);
        ctx.strokeStyle = '#1e293b';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();
        lastX = pos.x;
        lastY = pos.y;
    }

    function stopDraw(e) {
        if (!isDrawing) return;
        isDrawing = false;
        hiddenInput.value = canvas.toDataURL('image/png');
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDraw);
})();

function clearSignature() {
    const canvas = document.getElementById('signature-canvas');
    const ctx = canvas.getContext('2d');
    const hiddenInput = document.getElementById('signature-input');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hiddenInput.value = '';
}
</script>
@endsection