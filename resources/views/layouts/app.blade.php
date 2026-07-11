<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BAZMA Express') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navbar / Header -->
    <header class="glass-panel sticky top-0 z-40 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                        <span class="text-xl font-bold tracking-wider text-blue-500 uppercase">BAZMA</span>
                        <span class="text-xl font-semibold text-slate-300 uppercase">Express</span>
                    </a>
                </div>

                <!-- Navigation links / Profile -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="text-sm font-medium text-slate-300">
                            {{ Auth::user()->name }} 
                            <span class="text-xs text-blue-400 bg-blue-950 px-2 py-0.5 rounded-full border border-blue-900 ml-1">
                                {{ strtoupper(str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'Guest')) }}
                            </span>
                        </div>
                        
                        <!-- Panel link shortcut -->
                        @if(Auth::user()->hasRole('customer'))
                            <a href="{{ route('customer.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">Dashboard</a>
                        @elseif(Auth::user()->hasRole('admin_cabang'))
                            <a href="{{ route('branch.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">Admin Panel</a>
                        @elseif(Auth::user()->hasRole('kurir'))
                            <a href="{{ route('courier.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">Kurir Panel</a>
                        @elseif(Auth::user()->hasRole('manager'))
                            <a href="{{ route('manager.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">Manager Portal</a>
                        @elseif(Auth::user()->hasRole('owner'))
                            <a href="{{ route('owner.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">Owner Portal</a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs bg-red-950/40 text-red-400 hover:bg-red-900/60 border border-red-900/50 px-3 py-1.5 rounded transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded transition shadow-lg shadow-blue-950/50"> Daftar </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content wrapper -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Toast Alerts -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-950/40 border border-emerald-800 text-emerald-400 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium flex-grow">{{ session('success') }}</div>
                <button type="button" @click="show = false" class="text-emerald-400 hover:text-white focus:outline-none">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-950/40 border border-red-800 text-red-400 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-sm font-medium flex-grow">{{ session('error') }}</div>
                <button type="button" @click="show = false" class="text-red-400 hover:text-white focus:outline-none">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-panel border-t border-slate-800/80 py-6 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('company_name', 'BAZMA Express') }}. Dibuat untuk Tugas Praktek Pembuatan Aplikasi Web.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
