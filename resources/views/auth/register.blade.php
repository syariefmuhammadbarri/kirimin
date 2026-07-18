@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] py-8">
    <div class="w-full max-w-lg p-8 rounded-2xl glass-panel shadow-2xl border border-slate-800/80">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Daftar Akun Baru</h1>
            <p class="text-sm text-slate-600">Buat akun BAZMA Express Anda untuk memulai booking pengiriman</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Nama & Email (Two column layout on larger screens) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                </div>
            </div>

            <!-- Telepon & Kota -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon / WA</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 0812345678"
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-slate-700 mb-1.5">Kota</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}" required placeholder="Contoh: Jakarta"
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('city')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                <textarea id="address" name="address" required rows="3"
                          class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- reCAPTCHA Placeholder / Field if Enabled -->
            @if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    @error('recaptcha')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-150">
                    Daftar Akun
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-slate-400">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Masuk Di Sini
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endsection
