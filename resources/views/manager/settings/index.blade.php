@extends('layouts.app')

@section('content')
<div class="flex gap-6">
    {{-- Sidebar --}}
    @include('manager._sidebar')

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Sistem</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi pengaturan dinamis untuk sistem ekspedisi</p>
        </div>

        @if(session('success'))
        <div class="mb-6 px-5 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('manager.settings.update') }}" class="card-panel rounded-2xl border border-slate-200 p-6 space-y-6">
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
                <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $groupInfo['icon'] }}"/></svg>
                    {{ $groupInfo['label'] }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($grouped->get($groupKey, collect()) as $setting)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2" for="setting-{{ $setting->id }}">
                            {{ $setting->description ?: ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                        <input id="setting-{{ $setting->id }}" type="text" name="settings[{{ $setting->key }}][value]"
                               value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}"
                               class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- Fallback for ungrouped settings --}}
            @foreach($grouped as $groupKey => $groupSettings)
                @if(!isset($settingGroups[$groupKey]))
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        {{ ucfirst($groupKey) }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($groupSettings as $setting)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2" for="setting-{{ $setting->id }}">
                                {{ $setting->description ?: ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                            <input type="text" name="settings[{{ $setting->key }}][value]"
                                   value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}"
                                   class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            <div class="pt-4 border-t border-slate-200">
                <button type="submit"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection