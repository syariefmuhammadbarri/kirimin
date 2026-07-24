<aside class="hidden lg:flex flex-col w-52 flex-shrink-0 gap-1 pt-1">
    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Manager Portal</p>
    <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('manager.branches.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.branches.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Cabang
    </a>
    <a href="{{ route('manager.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.users.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Karyawan
    </a>
    <a href="{{ route('manager.customers.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.customers.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Pelanggan
    </a>
    <a href="{{ route('manager.vehicles.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.vehicles.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Kendaraan
    </a>
    <div class="my-2 h-px bg-slate-200"></div>
    <a href="{{ route('manager.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.settings.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Pengaturan
    </a>
    <a href="{{ route('manager.landing-contents.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('manager.landing-contents.*') ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium' }}">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Landing Page
    </a>
    <div class="my-2 h-px bg-slate-200"></div>
    <a href="{{ route('manager.report', request()->only(['start_date', 'end_date'])) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 hover:text-blue-600 hover:bg-blue-50 font-medium transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Unduh Laporan
    </a>
</aside>
