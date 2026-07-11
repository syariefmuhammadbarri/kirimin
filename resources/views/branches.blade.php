@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="text-center space-y-4">
        <h2 class="text-3xl font-bold text-white">Cabang & Outlet BAZMA Express</h2>
        <p class="text-slate-400 max-w-2xl mx-auto">Kami hadir di lokasi strategis kota-kota besar untuk memudahkan proses serah terima barang kiriman Anda.</p>
    </div>

    <div class="glass-panel rounded-2xl overflow-hidden border border-slate-800/80 shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/60">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Cabang</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kota</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Lengkap</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Telepon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-transparent">
                    @foreach($branches as $branch)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="px-6 py-4 font-semibold text-white">{{ $branch->name }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $branch->city }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $branch->address }}</td>
                            <td class="px-6 py-4 text-slate-300 font-mono">{{ $branch->phone }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('landing') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300 text-sm transition">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection