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
        <a href="{{ route('manager.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-blue-600/20 text-blue-400 border border-blue-800/40">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Karyawan
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.report') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Unduh Laporan
        </a>
    </aside>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-7 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Manajemen Karyawan</h1>
                <p class="text-sm text-slate-400 mt-1">{{ $users->count() }} karyawan aktif</p>
            </div>
            <a href="{{ route('manager.users.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Karyawan
            </a>
        </div>

        <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
            @if($users->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-slate-500">Belum ada karyawan. Tambahkan admin cabang atau kurir.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800">
                            <th class="px-6 py-3 text-left">Nama Karyawan</th>
                            <th class="px-6 py-3 text-left">Peran</th>
                            <th class="px-6 py-3 text-left">Cabang</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-white">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php $role = $user->roles->first(); @endphp
                                @if($role)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full uppercase border
                                    {{ $role->name === 'admin_cabang' ? 'bg-indigo-950/60 text-indigo-400 border-indigo-800/50' : 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-300">{{ $user->branch->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-400 text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.users.edit', $user) }}"
                                       class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.users.destroy', $user) }}"
                                          onsubmit="return confirm('Nonaktifkan karyawan {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs bg-red-950/60 hover:bg-red-900/80 text-red-400 px-3 py-1.5 rounded transition border border-red-900/40">
                                            Nonaktifkan
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
