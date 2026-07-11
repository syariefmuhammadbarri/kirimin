@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-52 flex-shrink-0 gap-1 pt-1">
        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider px-3 mb-2">Manager Portal</p>
        <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition {{ request()->routeIs('manager.dashboard') ? 'bg-blue-600/20 text-blue-400 border border-blue-800/40' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('manager.branches.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition {{ request()->routeIs('manager.branches.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-800/40' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Cabang
        </a>
        <a href="{{ route('manager.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition {{ request()->routeIs('manager.users.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-800/40' : '' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Karyawan
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.report') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Unduh Laporan
        </a>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 min-w-0">
        {{-- Header --}}
        <div class="mb-7 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Manajemen Cabang</h1>
                <p class="text-sm text-slate-400 mt-1">{{ $branches->count() }} cabang terdaftar</p>
            </div>
            <a href="{{ route('manager.branches.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Cabang
            </a>
        </div>

        {{-- Table --}}
        <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
            @if($branches->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-slate-500">Belum ada cabang. Tambahkan cabang pertama Anda.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800">
                            <th class="px-6 py-3 text-left">Nama Cabang</th>
                            <th class="px-6 py-3 text-left">Kota</th>
                            <th class="px-6 py-3 text-left">Telepon</th>
                            <th class="px-6 py-3 text-center">Karyawan</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($branches as $branch)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-white">{{ $branch->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $branch->address }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-300">{{ $branch->city }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $branch->phone }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-slate-300 text-xs font-bold">
                                    {{ $branch->users_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.branches.edit', $branch) }}"
                                       class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.branches.destroy', $branch) }}"
                                          onsubmit="return confirm('Hapus cabang {{ $branch->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs bg-red-950/60 hover:bg-red-900/80 text-red-400 px-3 py-1.5 rounded transition border border-red-900/40">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
