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
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-blue-400 bg-blue-600/20 border border-blue-800/40">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
        <div class="my-2 h-px bg-slate-800"></div>
        <a href="{{ route('manager.report') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Unduh Laporan
        </a>
    </aside>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Pengaturan Sistem</h1>
            <p class="text-sm text-slate-400 mt-1">Konfigurasi pengaturan dinamis untuk sistem ekspedisi</p>
        </div>

        @if(session('success'))
        <div class="mb-6 px-5 py-3 rounded-xl bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('manager.settings.update') }}" class="glass-panel rounded-2xl border border-slate-800 p-6 space-y-6">
            @csrf

            @php
            $settingGroups = [
                'perusahaan' => ['label' => 'Profil Perusahaan', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                'pembayaran' => ['label' => 'Pembayaran', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z'],
                'sosial' => ['label' => 'Media Sosial & Kontak', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ];
            $grouped = $settings->groupBy(function($s) {
                $parts = explode('_', $s->key);
                return $parts[0] ?? 'umum';
            });
            @endphp

            @foreach($settingGroups as $groupKey => $groupInfo)
            <div>
                <h2 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $groupInfo['icon'] }}"/></svg>
                    {{ $groupInfo['label'] }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($grouped->get($groupKey, collect()) as $setting)
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2" for="setting-{{ $setting->id }}">
                            {{ $setting->description ?: ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                        <input id="setting-{{ $setting->id }}" type="text" name="settings[{{ $setting->key }}][value]"
                               value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}"
                               class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- Fallback for ungrouped settings --}}
            @foreach($grouped as $groupKey => $groupSettings)
                @if(!isset($settingGroups[$groupKey]))
                <div>
                    <h2 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        {{ ucfirst($groupKey) }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($groupSettings as $setting)
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2" for="setting-{{ $setting->id }}">
                                {{ $setting->description ?: ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                            <input type="text" name="settings[{{ $setting->key }}][value]"
                                   value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}"
                                   class="w-full px-4 py-3 rounded-lg bg-slate-900/60 border border-slate-700/80 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            <div class="pt-4 border-t border-slate-800">
                <button type="submit"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/50 transition">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection