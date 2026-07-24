@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] py-8">
    <div class="w-full max-w-lg">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-3">Daftar Akun Baru</h1>
            <p class="text-sm text-slate-500">Pilih tipe akun yang ingin Anda buat</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium text-center max-w-md mx-auto">
                {{ session('status') }}
            </div>
        @endif

        <!-- Role Selection Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Staff Card -->
            <a href="{{ route('register.form', 'staff') }}" 
               class="group relative block p-8 rounded-2xl glass-panel border-2 border-slate-200 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-900/10 transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Daftar sebagai Staff</h2>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Admin Cabang, Kurir,<br>Manager, atau Owner
                    </p>
                    <div class="mt-6 w-full py-2.5 bg-blue-600 group-hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition text-center">
                        Daftar Staff
                    </div>
                </div>
            </a>

            <!-- Customer Card -->
            <a href="{{ route('register.form', 'customer') }}" 
               class="group relative block p-8 rounded-2xl glass-panel border-2 border-slate-200 hover:border-emerald-400 hover:shadow-xl hover:shadow-emerald-900/10 transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Daftar sebagai Customer</h2>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Buat akun customer<br>untuk booking & lacak paket
                    </p>
                    <div class="mt-6 w-full py-2.5 bg-emerald-600 group-hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition text-center">
                        Daftar Customer
                    </div>
                </div>
            </a>
        </div>

        <!-- Login link -->
        <div class="mt-10 text-center text-sm text-slate-500">
            Sudah punya akun? 
            <a href="{{ route('login.choose') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Masuk Di Sini
            </a>
        </div>
    </div>
</div>
@endsection