@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola informasi akun dan data pengiriman Anda</p>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="glass-panel rounded-2xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Foto Profil --}}
            <div class="flex items-center gap-5 mb-6 pb-6 border-b border-slate-200">
                <div class="relative">
                    @if($customer->photo_path)
                        <img src="{{ Storage::url($customer->photo_path) }}" alt="Foto Profil"
                             class="w-20 h-20 rounded-full object-cover border-2 border-slate-300">
                    @else
                        <div class="w-20 h-20 rounded-full bg-slate-200 border-2 border-slate-300 flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <label for="photo" class="cursor-pointer text-sm font-medium text-slate-700 hover:text-slate-900 border border-slate-300 rounded-lg px-3 py-2 transition">
                        Ganti Foto
                    </label>
                    <input id="photo" type="file" name="photo" accept="image/*" class="hidden">
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG, WEBP. Maks 2MB.</p>
                    @error('photo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="name">Nama Lengkap *</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 transition text-sm">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1.5">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-sm cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Email tidak dapat diubah melalui halaman ini.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="phone">Nomor Telepon</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $customer->phone) }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 transition text-sm">
                    @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="city">Kota</label>
                    <input id="city" type="text" name="city" value="{{ old('city', $customer->city) }}"
                           placeholder="Jakarta"
                           class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 transition text-sm">
                    @error('city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3"
                              placeholder="Jl. Contoh No. 1, RT 01/RW 01, Kelurahan, Kecamatan"
                              class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 transition text-sm resize-none">{{ old('address', $customer->address) }}</textarea>
                    @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-200 flex items-center justify-between gap-3">
                <a href="{{ route('customer.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700 transition">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Preview foto sebelum upload
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const img = document.querySelector('img[alt="Foto Profil"]');
        if (img) {
            img.src = ev.target.result;
        } else {
            const placeholder = document.querySelector('.w-20.h-20.rounded-full.bg-slate-200');
            if (placeholder) {
                placeholder.innerHTML = '<img src="' + ev.target.result + '" class="w-20 h-20 rounded-full object-cover">';
            }
        }
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
