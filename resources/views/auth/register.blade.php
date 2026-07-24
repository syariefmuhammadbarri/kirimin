@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="w-full max-w-md p-8 rounded-2xl card-panel shadow-sm border border-slate-200">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Daftar {{ $registerType === 'staff' ? 'Staff' : 'Customer' }}</h1>
            <p class="text-sm text-slate-500">
                {{ $registerType === 'staff' ? 'Buat akun staff BAZMA Express' : 'Buat akun customer BAZMA Express' }}
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="register_type" value="{{ $registerType }}">

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                </div>
            </div>

            <!-- reCAPTCHA -->
            @if(filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    @error('recaptcha')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
                    Daftar Akun
                </button>
            </div>
        </form>

        <!-- Back to role selection -->
        <div class="mt-6 text-center">
            <a href="{{ route('register.choose') }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Pilih tipe akun lain
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun? 
            <a href="{{ route('login.choose') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
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