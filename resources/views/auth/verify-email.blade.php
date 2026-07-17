@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] py-8">
    <div class="w-full max-w-xl p-8 rounded-2xl glass-panel shadow-2xl border border-slate-800/80">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Verifikasi Email</h1>
            <p class="text-sm text-slate-600">Silakan cek inbox email Anda dan klik tautan verifikasi untuk melanjutkan.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 rounded-lg bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="mb-6 p-6 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700">
            <p class="mb-4 text-sm leading-7">
                Terima kasih telah mendaftar. Sebelum melanjutkan, periksa email Anda untuk tautan verifikasi.
                Jika Anda tidak menerima email, klik tombol di bawah ini untuk mengirim ulang.
            </p>
            <p class="text-xs text-slate-500">
                Jika email belum sampai dalam beberapa menit, cek folder spam atau promosi.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
            @csrf
            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-150">
                Kirim ulang tautan verifikasi
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-600">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Keluar
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</div>
@endsection
