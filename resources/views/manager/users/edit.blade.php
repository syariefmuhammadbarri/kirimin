@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('manager.users.index') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Karyawan
        </a>
    </div>

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Edit Profil Karyawan</h1>
        <p class="text-sm text-slate-400 mt-1">{{ $user->name }}</p>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-800 p-6">
        <form method="POST" action="{{ route('manager.users.update', $user) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="name">Nama Lengkap *</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('name')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="email">Email *</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('email')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-500 mb-3">Ubah Password (kosongkan jika tidak ingin diubah)</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2" for="password">Password Baru</label>
                        <input id="password" type="password" name="password"
                               class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                               placeholder="Min. 6 karakter">
                        @error('password')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2" for="password_confirmation">Konfirmasi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                               placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="branch_id">Cabang *</label>
                <select id="branch_id" name="branch_id" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm appearance-none cursor-pointer">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} — {{ $branch->city }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Peran / Role *</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($roles as $role)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="{{ $role->name }}" class="sr-only peer"
                               {{ old('role', $user->roles->first()?->name) === $role->name ? 'checked' : '' }}>
                        <div class="glass-panel border-2 border-slate-700 peer-checked:border-blue-500 rounded-xl p-4 transition">
                            <div class="font-semibold text-white text-sm capitalize">{{ str_replace('_', ' ', $role->name) }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $role->name === 'admin_cabang' ? 'Kelola paket di cabang' : 'Antar paket ke pelanggan' }}
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('role')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('manager.users.index') }}" class="text-sm text-slate-400 hover:text-white transition">Batal</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-6 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
                    Perbarui Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
