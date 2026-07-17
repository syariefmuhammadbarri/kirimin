@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md p-8 rounded-2xl glass-panel shadow-2xl border border-slate-800/80">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Selamat Datang Kembali</h1>
            <p class="text-sm text-slate-600">Masuk ke akun BAZMA Express Anda</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-blue-950/40 border border-blue-900 text-blue-400 text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('email')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-500 transition">
                        Lupa Password?
                    </a>
                </div>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('password')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                <label for="remember" class="ml-2 block text-sm text-slate-700">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <!-- reCAPTCHA Placeholder / Field if Enabled -->
            @if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
                <input type="hidden" name="g-recaptcha-response" id="recaptcha-response">
                @error('recaptcha')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            @endif

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-150">
                    Masuk
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-slate-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'login'}).then(function(token) {
                    document.getElementById('recaptcha-response').value = token;
                    document.querySelector('form').submit();
                });
            });
        });
    </script>
@endif
@endsection
