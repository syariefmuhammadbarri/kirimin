@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Konten Landing Page</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola konten halaman utama (landing page) secara dinamis</p>
            </div>
            <a href="{{ route('manager.landing-contents.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Konten
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 px-5 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <div class="card-panel rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-base font-bold text-slate-900">Daftar Konten</h2>
                <span class="text-xs text-slate-500 font-medium">{{ $contents->count() }} item</span>
            </div>

            @if($contents->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-14 h-14 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-slate-500 font-medium">Belum ada konten landing page</p>
                <p class="text-slate-400 text-sm mt-1">Tambahkan konten baru untuk mengatur tampilan halaman utama</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 bg-slate-50/50">
                            <th class="px-6 py-3 text-left font-semibold">Section</th>
                            <th class="px-6 py-3 text-left font-semibold">Judul</th>
                            <th class="px-6 py-3 text-left font-semibold">Konten</th>
                            <th class="px-6 py-3 text-center font-semibold">Urutan</th>
                            <th class="px-6 py-3 text-center font-semibold">Aktif</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($contents as $content)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold uppercase px-2.5 py-1 rounded-full border bg-slate-100 text-slate-700 border-slate-200">
                                    {{ $content->section }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 font-semibold">{{ Str::limit($content->title, 40) }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs max-w-xs truncate">
                                {{ Str::limit(strip_tags($content->content), 60) }}
                            </td>
                            <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                {{ $content->order }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($content->is_active)
                                    <span class="text-emerald-700 text-xs font-semibold bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">✓ Aktif</span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.landing-contents.edit', $content) }}"
                                       class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-lg font-medium transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.landing-contents.destroy', $content) }}"
                                          onsubmit="return confirm('Hapus konten ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg font-medium transition">
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