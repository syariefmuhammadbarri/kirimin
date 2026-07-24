@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-7 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Karyawan</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar staf, kurir, dan admin cabang</p>
            </div>
            <a href="{{ route('manager.users.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Karyawan
            </a>
        </div>

        <div class="card-panel rounded-2xl border border-slate-200 overflow-hidden">
            @if($users->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-slate-400">Belum ada karyawan. Tambahkan admin cabang atau kurir.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-3 text-left font-semibold">Nama Karyawan</th>
                            <th class="px-6 py-3 text-left font-semibold">Peran</th>
                            <th class="px-6 py-3 text-left font-semibold">Cabang</th>
                            <th class="px-6 py-3 text-left font-semibold">Email</th>
                            <th class="px-6 py-3 text-center font-semibold">Status</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php $role = $user->roles->first(); @endphp
                                @if($role)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full uppercase border
                                    {{ $role->name === 'admin_cabang' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">{{ $user->branch->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($user->is_active)
                                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full">
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-xs font-semibold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('manager.users.toggle-active', $user) }}">
                                        @csrf
                                        <button type="submit" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-lg font-medium transition">
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('manager.users.edit', $user) }}"
                                       class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-lg font-medium transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus karyawan {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg font-medium transition">
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
