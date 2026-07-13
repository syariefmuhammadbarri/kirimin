<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kirimin') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col" data-lenis-prevent>
    <!-- Navbar / Header -->
    <header id="main-header" class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                        <span class="text-xl font-bold tracking-tight text-blue-600">Kirimin</span>
                    </a>
                </div>

                <!-- Navigation links / Profile -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="text-sm font-medium text-gray-700">
                            {{ Auth::user()->name }} 
                            <span class="text-xs text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200 ml-1">
                                {{ strtoupper(str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'Guest')) }}
                            </span>
                        </div>
                        
                        @if(Auth::user()->hasRole('customer'))
                            <a href="{{ route('customer.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Dashboard</a>
                        @elseif(Auth::user()->hasRole('admin_cabang'))
                            <a href="{{ route('branch.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Admin Panel</a>
                        @elseif(Auth::user()->hasRole('kurir'))
                            <a href="{{ route('courier.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Kurir Panel</a>
                        @elseif(Auth::user()->hasRole('manager'))
                            <a href="{{ route('manager.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Manager Portal</a>
                        @elseif(Auth::user()->hasRole('owner'))
                            <a href="{{ route('owner.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Owner Portal</a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition shadow-sm hover:shadow-md"> Daftar </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content wrapper -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium flex-grow">{{ session('success') }}</div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 focus:outline-none">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-sm font-medium flex-grow">{{ session('error') }}</div>
                <button type="button" @click="show = false" class="text-red-500 hover:text-red-700 focus:outline-none">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 py-8 text-center text-sm text-gray-500 mt-auto bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Kirimin') }}. Dibuat untuk Tugas Praktek Pembuatan Aplikasi Web.</p>
        </div>
    </footer>

    @yield('scripts')

    <noscript>Your browser does not support JavaScript, but smooth scroll will still work with native CSS.</noscript>
</body>
</html>