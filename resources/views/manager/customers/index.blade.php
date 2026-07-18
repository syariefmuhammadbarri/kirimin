@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Manajemen Customer</h1>
        <p class="text-sm text-slate-400 mt-0.5">Pantau dan moderasi akun customer</p>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('manager.customers.index') }}" class="mb-6 flex gap-2">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Cari nama, email, atau nomor HP..."
               class="flex-1 px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-slate-500 text-sm">
        <button type="submit" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition">
            Cari
        </button>
        @if($search)
        <a href="{{ route('manager.customers.index') }}" class="px-4 py-2.5 text-slate-400 hover:text-slate-200 border border-slate-700 rounded-lg transition text-sm">
            Reset
        </a>
        @endif
    </form>

    @if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-900/40 border border-emerald-700/50 p-3 text-sm text-emerald-300">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 rounded-lg bg-red-900/40 border border-red-700/50 p-3 text-sm text-red-300">
        {{ session('error') }}
    </div>
    @endif

    <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-300">Daftar Customer</h2>
            <span class="text-xs text-slate-500">{{ $customers->total() }} akun</span>
        </div>

        @if($customers->isEmpty())
        <div class="py-12 text-center text-slate-500">
            <p class="font-medium">Tidak ada customer ditemukan</p>
            @if($search)<p class="text-sm mt-1">Coba kata kunci lain</p>@endif
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                        <th class="px-5 py-3 text-left font-medium">Customer</th>
                        <th class="px-5 py-3 text-left font-medium">Kontak</th>
                        <th class="px-5 py-3 text-center font-medium">Total Paket</th>
                        <th class="px-5 py-3 text-center font-medium">Status</th>
                        <th class="px-5 py-3 text-center font-medium">Bergabung</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-slate-800/20 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($customer->photo_path)
                                    <img src="{{ Storage::url($customer->photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center">
                                        <span class="text-xs text-slate-300 font-medium">
                                            {{ strtoupper(substr($customer->user?->name ?? $customer->phone, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-200">{{ $customer->user?->name ?? '(Tanpa Akun)' }}</p>
                                    <p class="text-xs text-slate-500">{{ $customer->user?->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-400">
                            <p>{{ $customer->phone ?: '-' }}</p>
                            <p class="text-xs text-slate-600">{{ $customer->city ?: '-' }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="font-semibold text-slate-300">{{ $customer->shipments_count }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($customer->is_suspended)
                                <span class="text-xs font-semibold bg-red-950/60 text-red-400 border border-red-800/50 px-2.5 py-1 rounded-full">
                                    Suspended
                                </span>
                            @else
                                <span class="text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50 px-2.5 py-1 rounded-full">
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-xs text-slate-500">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <form method="POST" action="{{ route('manager.customers.toggle-suspend', $customer) }}"
                                  onsubmit="return confirm('{{ $customer->is_suspended ? 'Aktifkan kembali' : 'Suspend' }} akun customer ini?')">
                                @csrf
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded border transition {{ $customer->is_suspended
                                            ? 'bg-emerald-950/40 text-emerald-400 border-emerald-800/50 hover:bg-emerald-900/50'
                                            : 'bg-red-950/40 text-red-400 border-red-800/50 hover:bg-red-900/50' }}">
                                    {{ $customer->is_suspended ? 'Aktifkan' : 'Suspend' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-800">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
