@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-52 flex-shrink-0 gap-1 pt-1">
        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider px-3 mb-2">Manager Portal</p>
        <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('manager.branches.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Cabang
        </a>
        <a href="{{ route('manager.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Karyawan
        </a>
        <a href="{{ route('manager.vehicles.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Kendaraan
        </a>
        <a href="{{ route('manager.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
        <a href="{{ route('manager.landing-contents.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-blue-400 bg-blue-600/20 border border-blue-800/40">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Landing Page
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.report') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Unduh Laporan
        </a>
    </aside>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Konten Landing Page</h1>
                <p class="text-sm text-slate-400 mt-1">Kelola konten halaman utama (landing page) secara dinamis</p>
            </div>
            <a href="{{ route('manager.landing-contents.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Konten
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 px-5 py-3 rounded-xl bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">Daftar Konten</h2>
                <span class="text-xs text-slate-500">{{ $contents->count() }} item</span>
            </div>

            @if($contents->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-14 h-14 text-slate-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-slate-400 font-medium">Belum ada konten landing page</p>
                <p class="text-slate-600 text-sm mt-1">Tambahkan konten baru untuk mengatur tampilan halaman utama</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800/60">
                            <th class="px-6 py-3 text-left font-medium">Section</th>
                            <th class="px-6 py-3 text-left font-medium">Judul</th>
                            <th class="px-6 py-3 text-left font-medium">Konten</th>
                            <th class="px-6 py-3 text-center font-medium">Urutan</th>
                            <th class="px-6 py-3 text-center font-medium">Aktif</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($contents as $content)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold uppercase px-2 py-1 rounded bg-slate-800 text-slate-300">
                                    {{ $content->section }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ Str::limit($content->title, 40) }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-xs max-w-xs truncate">
                                {{ Str::limit(strip_tags($content->content), 60) }}
                            </td>
                            <td class="px-6 py-4 text-center text-slate-300">
                                {{ $content->order }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($content->is_active)
                                    <span class="text-emerald-400 text-xs font-semibold">✓ Aktif</span>
                                @else
                                    <span class="text-slate-600 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.landing-contents.edit', $content) }}"
                                       class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded transition font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.landing-contents.destroy', $content) }}"
                                          onsubmit="return confirm('Hapus konten ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-900/50 hover:bg-red-800 text-red-300 px-3 py-1.5 rounded transition font-medium">
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