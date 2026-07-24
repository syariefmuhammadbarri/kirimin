@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="w-full max-w-md p-8 rounded-2xl card-panel shadow-sm border border-slate-200">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">{{ $loginType === 'staff' ? 'Login Staff' : 'Login Customer' }}</h1>
            <p class="text-sm text-slate-500">
                {{ $loginType === 'staff' ? 'Masuk sebagai Admin, Kurir, Manager, atau Owner' : 'Masuk ke akun customer BAZMA Express Anda' }}
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="login_type" value="{{ $loginType }}">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('email')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                        Lupa Password?
                    </a>
                </div>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('password')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 bg-white text-blue-600 focus:ring-blue-500">
                <label for="remember" class="ml-2 block text-sm text-slate-700">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <!-- reCAPTCHA v2 Checkbox -->
            @if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    @error('recaptcha')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    Masuk
                </button>
            </div>
        </form>

        <!-- Back to role selection -->
        <div class="mt-6 text-center">
            <a href="{{ route('login.choose') }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Pilih tipe login lain
            </a>
        </div>

        <!-- Footer - Only show register for customer -->
        @if($loginType === 'customer')
        <div class="mt-6 text-center text-sm text-slate-600">
            Belum punya akun customer? 
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
                Daftar Sekarang
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endsection