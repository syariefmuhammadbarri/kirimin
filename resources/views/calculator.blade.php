@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="text-center space-y-4">
        <h2 class="text-3xl font-bold text-slate-900">Kalkulator Ongkir</h2>
        <p class="text-slate-500">Hitung estimasi biaya pengiriman paket Anda</p>
    </div>

    <div class="card-panel rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm border border-slate-200"
         x-data="{
            origin: '',
            destination: '',
            weight: 1.0,
            serviceType: 'regular',
            loading: false,
            result: null,
            errorMsg: '',
            calcRate() {
                if(!this.origin || !this.destination || this.weight <= 0) {
                    this.errorMsg = 'Lengkapi kota asal, kota tujuan dan berat paket.';
                    return;
                }
                this.errorMsg = '';
                this.loading = true;
                this.result = null;
                
                fetch('{{ route('customer.calculate-rate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        origin: this.origin,
                        destination: this.destination,
                        weight: this.weight,
                        service_type: this.serviceType
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if(data.errors) {
                        this.errorMsg = 'Gagal melakukan kalkulasi tarif.';
                    } else {
                        this.result = data;
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.errorMsg = 'Koneksi bermasalah.';
                });
            }
         }">
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Kota Asal</label>
                    <select x-model="origin" class="bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full text-sm">
                        <option value="">Pilih Kota Asal</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Kota Tujuan</label>
                    <select x-model="destination" class="bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full text-sm">
                        <option value="">Pilih Kota Tujuan</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Estimasi Berat (Kg)</label>
                    <input type="number" step="0.1" min="0.1" x-model="weight" class="bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Jenis Layanan</label>
                    <select x-model="serviceType" class="bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full text-sm">
                        <option value="regular">Regular Service</option>
                        <option value="express">Express Delivery</option>
                    </select>
                </div>
            </div>

            <button type="button" @click="calcRate()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl w-full text-sm transition shadow-sm">
                Hitung Ongkos Kirim
            </button>

            <!-- Results / Feedback -->
            <div x-show="loading" class="text-center py-4">
                <span class="text-sm text-blue-600 font-medium">Menghitung tarif...</span>
            </div>

            <div x-show="errorMsg" class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-medium" x-text="errorMsg"></div>

            <div x-show="result" class="p-5 bg-slate-50 border border-slate-200 rounded-xl space-y-3" x-transition>
                <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider">Hasil Estimasi Tarif</h3>
                <div class="grid grid-cols-3 gap-3 text-center text-xs">
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                        <div class="text-slate-500 mb-1">Tarif / Kg</div>
                        <div class="text-sm font-semibold text-slate-900">Rp <span x-text="Number(result.price_per_kg).toLocaleString('id-ID')"></span></div>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                        <div class="text-slate-500 mb-1">Durasi</div>
                        <div class="text-sm font-semibold text-slate-900"><span x-text="result.estimated_days"></span> Hari</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                        <div class="text-slate-500 mb-1">Total Ongkir</div>
                        <div class="text-sm font-bold text-emerald-600">Rp <span x-text="Number(result.total_price).toLocaleString('id-ID')"></span></div>
                    </div>
                </div>
                <div x-show="!result.route_found" class="text-[11px] text-slate-500 text-center">
                    * Menggunakan tarif rute default (Rute belum tersertifikasi di database).
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('landing') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold transition">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection