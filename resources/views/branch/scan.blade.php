@extends('layouts.app')

@section('styles')
<style>
    @keyframes scanLine {
        0% { top: 0; }
        50% { top: calc(100% - 2px); }
        100% { top: 0; }
    }
    .scan-line {
        position: absolute;
        left: 0; right: 0; height: 2px;
        background: linear-gradient(to right, transparent, #3b82f6, transparent);
        animation: scanLine 2s ease-in-out infinite;
    }
    .scan-corner { position: absolute; width: 20px; height: 20px; border-color: #3b82f6; border-style: solid; }
    .corner-tl { top: 0; left: 0; border-width: 3px 0 0 3px; }
    .corner-tr { top: 0; right: 0; border-width: 3px 3px 0 0; }
    .corner-bl { bottom: 0; left: 0; border-width: 0 0 3px 3px; }
    .corner-br { bottom: 0; right: 0; border-width: 0 3px 3px 0; }
</style>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('branch.dashboard') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Scan Paket Masuk</h1>
        <p class="text-sm text-slate-400 mt-1">Scan QR Code atau masukkan kode booking secara manual</p>
    </div>

    {{-- QR Scanner Visual --}}
    <div class="glass-panel rounded-2xl border border-slate-800 p-8 mb-6 flex flex-col items-center">
        <div class="relative w-48 h-48 bg-slate-900/60 rounded-xl border border-slate-700 mb-6 overflow-hidden">
            <div class="scan-line"></div>
            <div class="scan-corner corner-tl"></div>
            <div class="scan-corner corner-tr"></div>
            <div class="scan-corner corner-bl"></div>
            <div class="scan-corner corner-br"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-16 h-16 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5A7.5 7.5 0 117.5 9 7.5 7.5 0 0121 15.5z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-slate-500 text-center">Arahkan kamera ke QR Code pada label paket</p>
        <p class="text-xs text-slate-600 mt-1 text-center">(Fitur kamera memerlukan integrasi kamera browser)</p>
    </div>

    {{-- Divider --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="flex-1 h-px bg-slate-800"></div>
        <span class="text-xs text-slate-500 uppercase tracking-wider">atau masukkan manual</span>
        <div class="flex-1 h-px bg-slate-800"></div>
    </div>

    {{-- Manual Input Form --}}
    <div class="glass-panel rounded-2xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('branch.scan.process') }}">
            @csrf
            <div class="mb-5">
                <label for="booking_code" class="block text-sm font-medium text-slate-700 mb-2">
                    Kode Booking / Nomor Resi
                </label>
                <input id="booking_code" type="text" name="booking_code"
                       value="{{ old('booking_code') }}" autofocus required
                       placeholder="Contoh: BK-20260702-ABCDE atau EXP-20260702-ABCDE"
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition text-sm font-mono">
                @error('booking_code')
                    <p class="mt-1.5 text-xs text-slate-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg shadow-lg shadow-slate-900/10 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Paket
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
        <p class="font-medium text-slate-300 mb-2">Panduan Penggunaan:</p>
        <p>• <strong class="text-slate-300">Kode Booking</strong>: format BK-YYYYMMDD-XXXXX</p>
        <p>• <strong class="text-slate-300">Nomor Resi</strong>: format EXP-YYYYMMDD-XXXXX</p>
        <p>• Paket akan otomatis diasosiasikan ke cabang Anda saat pertama kali diproses</p>
    </div>
</div>
@endsection
