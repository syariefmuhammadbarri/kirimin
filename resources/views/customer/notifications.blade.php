@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pembaruan status paket Anda</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('customer.notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg px-4 py-2 transition">
                Tandai semua dibaca
            </button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="glass-panel rounded-2xl border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-slate-500 font-medium">Belum ada notifikasi</p>
            <p class="text-slate-400 text-sm mt-1">Notifikasi akan muncul saat status paket Anda berubah</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
            @php
                $data = $notification->data;
                $isRead = $notification->read_at !== null;
                $statusMap = [
                    'booking_created'        => ['label' => 'Booking Dibuat', 'color' => 'text-slate-600'],
                    'waiting_dropoff'        => ['label' => 'Menunggu Drop-off', 'color' => 'text-amber-600'],
                    'pickup_scheduled'       => ['label' => 'Penjemputan Dijadwalkan', 'color' => 'text-amber-600'],
                    'received_at_branch'     => ['label' => 'Diterima di Cabang', 'color' => 'text-indigo-600'],
                    'out_for_delivery'       => ['label' => 'Dalam Pengantaran', 'color' => 'text-cyan-600'],
                    'delivered'              => ['label' => 'Terkirim ✓', 'color' => 'text-emerald-600'],
                    'cancelled'              => ['label' => 'Dibatalkan', 'color' => 'text-red-600'],
                    'gagal_kirim'            => ['label' => 'Gagal Kirim', 'color' => 'text-red-500'],
                ];
                $statusInfo = $statusMap[$data['status'] ?? ''] ?? ['label' => str_replace('_', ' ', $data['status'] ?? ''), 'color' => 'text-slate-600'];
            @endphp
            <div class="glass-panel rounded-xl border {{ $isRead ? 'border-slate-200 bg-white/50' : 'border-blue-200/60 bg-blue-50/30' }} p-4 transition">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2 {{ $isRead ? 'bg-slate-300' : 'bg-blue-500' }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold {{ $statusInfo['color'] }} uppercase tracking-wider">
                                {{ $statusInfo['label'] }}
                            </span>
                            <span class="text-xs text-slate-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-700 font-mono">{{ $data['tracking_number'] ?? '-' }}</p>
                        <p class="text-sm text-slate-600 mt-0.5">{{ $data['description'] ?? '' }}</p>
                        @if(!empty($data['location']))
                        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $data['location'] }}
                        </p>
                        @endif
                    </div>
                    @if(!$isRead)
                    <form method="POST" action="{{ route('customer.notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="flex-shrink-0 text-xs text-blue-500 hover:text-blue-700 transition">
                            Tandai dibaca
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
