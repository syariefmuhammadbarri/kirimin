@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="glass-panel rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl border border-slate-800">
        <h2 class="text-xl font-bold text-white uppercase border-b border-slate-800 pb-4">Lacak Pengiriman Paket</h2>
        
        <form action="{{ route('track.public') }}" method="GET" class="space-y-4">
            <div class="flex space-x-2">
                <input type="text" name="tracking_number" required value="{{ $trackingNumber }}" placeholder="Masukkan Nomor Resi / Kode Booking" 
                    class="bg-slate-900 border border-slate-800 rounded-lg px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-blue-500 flex-grow text-sm transition">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-lg text-sm transition">
                    Lacak
                </button>
            </div>
        </form>
    </div>

    @if($trackingNumber)
        @if($shipment)
            <div class="glass-panel rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl border border-slate-800">
                <div class="flex justify-between items-start border-b border-slate-800 pb-4 flex-wrap gap-4">
                    <div>
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Nomor Resi Pengiriman</div>
                        <h3 class="text-lg font-bold text-blue-400 font-mono">{{ $shipment->tracking_number }}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Status Paket</div>
                        <span class="inline-block bg-blue-950 border border-blue-900 text-blue-400 text-xs font-bold uppercase px-3 py-1 rounded-full">
                            {{ str_replace('_', ' ', $shipment->status) }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 text-xs bg-slate-900/50 p-4 rounded-xl border border-slate-800/80">
                    <div>
                        <span class="text-slate-500 block mb-1">PENGIRIM</span>
                        <strong class="text-slate-200">{{ substr($shipment->sender_name, 0, 2) . str_repeat('*', strlen($shipment->sender_name)-2) }}</strong>
                        <span class="text-slate-400 block mt-0.5">{{ $shipment->origin_city }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">PENERIMA</span>
                        <strong class="text-slate-200">{{ substr($shipment->receiver_name, 0, 2) . str_repeat('*', strlen($shipment->receiver_name)-2) }}</strong>
                        <span class="text-slate-400 block mt-0.5">{{ $shipment->destination_city }}</span>
                    </div>
                    <div class="mt-2 border-t border-slate-800/80 pt-2 col-span-2 flex justify-between">
                        <div>
                            <span class="text-slate-500 block">LAYANAN</span>
                            <strong class="text-slate-200 uppercase">{{ $shipment->service_type }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-right">BERAT</span>
                            <strong class="text-slate-200">{{ number_format($shipment->actual_weight ?: $shipment->estimated_weight, 2) }} kg</strong>
                        </div>
                    </div>
                </div>

                <!-- Timeline check points -->
                <div class="space-y-6 pt-4">
                    <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Histori Transit</h4>
                    
                    <div class="relative pl-6 border-l-2 border-slate-800 space-y-8 ml-3">
                        @foreach($shipment->trackings as $tracking)
                            <div class="relative">
                                <!-- Bullet Dot -->
                                <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-slate-900 bg-blue-500 shadow-md shadow-blue-900/50"></div>
                                
                                <div class="space-y-1">
                                    <span class="text-[10px] font-mono text-slate-500 bg-slate-900 border border-slate-800/60 px-2 py-0.5 rounded-full">
                                        {{ $tracking->tracked_at->format('d M Y H:i:s') }}
                                    </span>
                                    <div class="text-sm font-semibold text-slate-200">{{ $tracking->location }}</div>
                                    <p class="text-xs text-slate-400">{{ $tracking->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-red-950/20 border border-red-900/40 text-center text-red-400 space-y-2">
                <svg class="h-8 w-8 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="font-semibold text-sm">Resi Tidak Ditemukan</div>
                <p class="text-xs text-slate-500">Nomor resi atau kode booking "{{ $trackingNumber }}" tidak terdaftar dalam database kami. Pastikan format penulisan sudah benar.</p>
            </div>
        @endif
    @endif
</div>
@endsection
