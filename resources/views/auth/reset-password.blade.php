@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md p-8 rounded-2xl glass-panel shadow-2xl border border-slate-800/80">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-950/60 border border-blue-900/50 mb-4">
                <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Buat Password Baru</h1>
            <p class="text-sm text-slate-600">Pastikan password baru Anda aman dan mudah Anda ingat.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <!-- Token (hidden) -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                       placeholder="Ulangi password baru">
                @error('password_confirmation')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-150">
                Reset Password
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center text-sm text-slate-600">
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Kembali ke halaman masuk
            </a>
        </div>
    </div>
</div>
@endsection
