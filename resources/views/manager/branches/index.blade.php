@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main Content --}}
    <div class="flex-1 min-w-0">
        {{-- Header --}}
        <div class="mb-7 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Cabang</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $branches->count() }} cabang terdaftar</p>
            </div>
            <a href="{{ route('manager.branches.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Cabang
            </a>
        </div>

        {{-- Table --}}
        <div class="card-panel rounded-2xl border border-slate-200 overflow-hidden">
            @if($branches->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-slate-400">Belum ada cabang. Tambahkan cabang pertama Anda.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-3 text-left font-semibold">Nama Cabang</th>
                            <th class="px-6 py-3 text-left font-semibold">Kota</th>
                            <th class="px-6 py-3 text-left font-semibold">Telepon</th>
                            <th class="px-6 py-3 text-center font-semibold">Karyawan</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($branches as $branch)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $branch->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $branch->address }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $branch->city }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $branch->phone }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold">
                                    {{ $branch->users_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.branches.edit', $branch) }}"
                                       class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 font-medium transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.branches.destroy', $branch) }}"
                                          onsubmit="return confirm('Hapus cabang {{ $branch->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg border border-red-200 font-medium transition">
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
