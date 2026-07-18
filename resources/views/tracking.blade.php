@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900 uppercase border-b border-gray-100 pb-4">Lacak Pengiriman Paket</h2>
        
        <form action="{{ route('track.public') }}" method="GET" class="space-y-4">
            <div class="flex space-x-2">
                <input type="text" name="tracking_number" required value="{{ $trackingNumber }}" placeholder="Masukkan Nomor Resi / Kode Booking" 
                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 flex-grow text-sm transition">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white font-semibold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                    Lacak
                </button>
            </div>
        </form>
    </div>

    @if($trackingNumber)
        @if($shipment)
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
                <div class="flex justify-between items-start border-b border-gray-100 pb-4 flex-wrap gap-4">
                    <div>
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Nomor Resi Pengiriman</div>
                        <h3 class="text-lg font-bold text-blue-600 font-mono">{{ $shipment->tracking_number }}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Status Paket</div>
                        <span class="inline-block bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold uppercase px-3 py-1 rounded-full">
                            {{ str_replace('_', ' ', $shipment->status) }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <span class="text-gray-400 block mb-1">PENGIRIM</span>
                        <strong class="text-gray-800">{{ substr($shipment->sender_name, 0, 2) . str_repeat('*', strlen($shipment->sender_name)-2) }}</strong>
                        <span class="text-gray-500 block mt-0.5">{{ $shipment->origin_city }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">PENERIMA</span>
                        <strong class="text-gray-800">{{ substr($shipment->receiver_name, 0, 2) . str_repeat('*', strlen($shipment->receiver_name)-2) }}</strong>
                        <span class="text-gray-500 block mt-0.5">{{ $shipment->destination_city }}</span>
                    </div>
                    <div class="mt-2 border-t border-gray-200 pt-2 col-span-2 flex justify-between">
                        <div>
                            <span class="text-gray-400 block">LAYANAN</span>
                            <strong class="text-gray-800 uppercase">{{ $shipment->service_type }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-right">BERAT</span>
                            <strong class="text-gray-800">{{ number_format($shipment->actual_weight ?: $shipment->estimated_weight, 2) }} kg</strong>
                        </div>
                    </div>
                </div>

                <!-- Rute Saran & Estimasi Transit -->
                @php
                    $branches = \App\Models\Branch::all();
                    $suggestedHops = [];
                    // Origin City
                    $suggestedHops[] = [
                        'city' => $shipment->origin_city,
                        'name' => 'Cabang Pengirim (' . $shipment->origin_city . ')',
                    ];

                    // Check if Bandung is an intermediate city (e.g. Jakarta/Medan to/from Surabaya)
                    $originLower = strtolower($shipment->origin_city);
                    $destLower = strtolower($shipment->destination_city);

                    if (($originLower === 'jakarta' || $originLower === 'medan') && $destLower === 'surabaya') {
                        $bdgBranch = $branches->first(fn($b) => strtolower($b->city) === 'bandung');
                        if ($bdgBranch) {
                            $suggestedHops[] = [
                                'city' => 'Bandung',
                                'name' => 'Hub Transit: ' . $bdgBranch->name,
                            ];
                        }
                    } elseif ($originLower === 'surabaya' && ($destLower === 'jakarta' || $destLower === 'medan')) {
                        $bdgBranch = $branches->first(fn($b) => strtolower($b->city) === 'bandung');
                        if ($bdgBranch) {
                            $suggestedHops[] = [
                                'city' => 'Bandung',
                                'name' => 'Hub Transit: ' . $bdgBranch->name,
                            ];
                        }
                    }

                    // Destination City
                    $suggestedHops[] = [
                        'city' => $shipment->destination_city,
                        'name' => 'Cabang Penerima (' . $shipment->destination_city . ')',
                    ];
                @endphp

                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Rute Saran & Hops Transit</h4>
                    <div class="flex items-center gap-3 bg-blue-50/50 p-4 rounded-xl border border-blue-100/60 overflow-x-auto">
                        @foreach($suggestedHops as $index => $hop)
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <div class="text-left bg-white px-3 py-2 rounded-lg border border-gray-200">
                                    <span class="text-xs font-semibold text-gray-900 block">{{ $hop['city'] }}</span>
                                    <span class="text-[10px] text-gray-500 block">{{ $hop['name'] }}</span>
                                </div>
                                @if($index < count($suggestedHops) - 1)
                                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Timeline check points -->
                <div class="space-y-6 pt-4">
                    <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Histori Transit</h4>
                    
                    <div class="relative pl-6 border-l-2 border-gray-100 space-y-8 ml-3">
                        @foreach($shipment->trackings as $tracking)
                            <div class="relative">
                                <!-- Bullet Dot -->
                                <div class="absolute -left-[31px] top-1.5 h-4 w-4 rounded-full border-2 border-white bg-blue-600 shadow-sm"></div>
                                
                                <div class="space-y-1">
                                    <span class="text-[10px] font-mono text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full">
                                        {{ $tracking->tracked_at->format('d M Y H:i:s') }}
                                    </span>
                                    <div class="text-sm font-semibold text-gray-900">{{ $tracking->location }}</div>
                                    <p class="text-xs text-gray-500">{{ $tracking->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-red-50 border border-red-100 text-center text-red-700 space-y-2">
                <svg class="h-8 w-8 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="font-semibold text-sm">Resi Tidak Ditemukan</div>
                <p class="text-xs text-gray-500">Nomor resi atau kode booking "{{ $trackingNumber }}" tidak terdaftar dalam database kami. Pastikan format penulisan sudah benar.</p>
            </div>
        @endif
    @endif
</div>
@endsection
