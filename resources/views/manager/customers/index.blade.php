@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Customer</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau dan moderasi akun customer</p>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('manager.customers.index') }}" class="mb-6 flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Cari nama, email, atau nomor HP..."
                   class="flex-1 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Cari
            </button>
            @if($search)
            <a href="{{ route('manager.customers.index') }}" class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 border border-slate-200 rounded-xl transition text-sm font-medium">
                Reset
            </a>
            @endif
        </form>

        @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700 font-medium">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700 font-medium">
            {{ session('error') }}
        </div>
        @endif

        <div class="card-panel rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900">Daftar Customer</h2>
                <span class="text-xs text-slate-500 font-medium">{{ $customers->total() }} akun</span>
            </div>

            @if($customers->isEmpty())
            <div class="py-12 text-center text-slate-400">
                <p class="font-medium">Tidak ada customer ditemukan</p>
                @if($search)<p class="text-sm mt-1">Coba kata kunci lain</p>@endif
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 bg-slate-50/50">
                            <th class="px-5 py-3 text-left font-semibold">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold">Kontak</th>
                            <th class="px-5 py-3 text-center font-semibold">Total Paket</th>
                            <th class="px-5 py-3 text-center font-semibold">Status</th>
                            <th class="px-5 py-3 text-center font-semibold">Bergabung</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($customer->photo_path)
                                        <img src="{{ Storage::url($customer->photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                                            <span class="text-xs font-bold">
                                                {{ strtoupper(substr($customer->user?->name ?? $customer->phone, 0, 2)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $customer->user?->name ?? '(Tanpa Akun)' }}</p>
                                        <p class="text-xs text-slate-500">{{ $customer->user?->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <p class="font-medium">{{ $customer->phone ?: '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $customer->city ?: '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-semibold text-slate-900">{{ $customer->shipments_count }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($customer->is_suspended)
                                    <span class="text-xs font-semibold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full">
                                        Suspended
                                    </span>
                                @else
                                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs text-slate-500 font-medium">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('manager.customers.toggle-suspend', $customer) }}"
                                      onsubmit="return confirm('{{ $customer->is_suspended ? 'Aktifkan kembali' : 'Suspend' }} akun customer ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-lg border font-medium transition {{ $customer->is_suspended
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                                : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' }}">
                                        {{ $customer->is_suspended ? 'Aktifkan' : 'Suspend' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-200">
                {{ $customers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
