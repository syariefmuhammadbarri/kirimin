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
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .glass-panel {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #dbeafe;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col" data-lenis-prevent>
    <!-- Navbar / Header -->
    <header id="main-header" class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-blue-100/80 transition-all duration-300">
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

                            {{-- FR-08: Notification bell badge --}}
                            @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                            <a href="{{ route('customer.notifications') }}" class="relative inline-flex items-center text-gray-500 hover:text-blue-600 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if($unreadCount > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white text-[9px] font-bold">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </a>

                            {{-- FR-11: Link profil --}}
                            <a href="{{ route('customer.profile') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Profil</a>
                        @elseif(Auth::user()->hasRole('admin_cabang'))
                            <a href="{{ route('branch.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Admin Panel</a>
                            {{-- FR-07: Walk-in booking shortcut --}}
                            <a href="{{ route('branch.booking.walkin') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800 border border-emerald-200 bg-emerald-50 px-2.5 py-1 rounded-lg transition">Walk-in</a>
                        @elseif(Auth::user()->hasRole('kurir'))
                            <a href="{{ route('courier.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Kurir Panel</a>
                        @elseif(Auth::user()->hasRole('manager'))
                            <a href="{{ route('manager.dashboard') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Manager Portal</a>
                            <a href="{{ route('manager.customers.index') }}" class="text-xs font-medium text-gray-600 hover:text-blue-600 transition">Customer</a>
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
                        <a href="{{ route('login.choose') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Masuk</a>
                        <a href="{{ route('register.choose') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition shadow-sm hover:shadow-md"> Daftar </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content wrapper -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        @if (session('warning'))
            <x-alert type="warning">{{ session('warning') }}</x-alert>
        @endif

        @if (session('info'))
            <x-alert type="info">{{ session('info') }}</x-alert>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-blue-100 py-8 text-center text-sm text-slate-600 mt-auto bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Kirimin') }}. Dibuat untuk Tugas Praktek Pembuatan Aplikasi Web.</p>
        </div>
    </footer>

    @yield('scripts')

    <noscript>Your browser does not support JavaScript, but smooth scroll will still work with native CSS.</noscript>
</body>
</html>