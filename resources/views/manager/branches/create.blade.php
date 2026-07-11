@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('manager.branches.index') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Cabang
        </a>
    </div>

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Tambah Cabang Baru</h1>
        <p class="text-sm text-slate-400 mt-1">Isi informasi cabang BAZMA Express</p>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-800 p-6">
        <form method="POST" action="{{ route('manager.branches.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="name">Nama Cabang *</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="Contoh: Cabang Jakarta Selatan">
                @error('name')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="city">Kota *</label>
                <input id="city" type="text" name="city" value="{{ old('city') }}" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="Contoh: Jakarta">
                @error('city')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="address">Alamat Lengkap *</label>
                <textarea id="address" name="address" rows="3" required
                          class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm resize-none"
                          placeholder="Jl. ...">{{ old('address') }}</textarea>
                @error('address')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="phone">Nomor Telepon *</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="08xx-xxxx-xxxx">
                @error('phone')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('manager.branches.index') }}" class="text-sm text-slate-400 hover:text-white transition">Batal</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-6 py-2.5 rounded-lg shadow-lg shadow-blue-950/50 transition">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
