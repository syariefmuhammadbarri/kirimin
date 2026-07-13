@extends('layouts.app')

@section('styles')
<style>
    .form-input {
        @apply w-full px-4 py-3 rounded-lg bg-white border border-gray-200 text-gray-900 placeholder-gray-400
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm shadow-sm;
    }
    .form-label { @apply block text-sm font-semibold text-gray-700 mb-2; }
    .form-error { @apply mt-1.5 text-xs text-red-600; }
    .section-panel { @apply card-panel rounded-2xl border border-gray-200 p-6 mb-6; }
    .rate-result { display: none; }
    .rate-result.visible { display: block; }

    /* Service Type Card */
    .service-card {
        border: 2px solid rgba(0, 0, 0, 0.06);
        background: #ffffff;
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .service-card:hover {
        border-color: rgba(37, 99, 235, 0.3);
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
        transform: translateY(-2px);
    }
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(37, 99, 235, 0.03) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .service-card:hover::before {
        opacity: 1;
    }
    .service-card .check-icon {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .service-card.selected {
        border-color: #2563eb;
        background: #f8faff;
        box-shadow: 0 4px 24px rgba(37, 99, 235, 0.12);
    }
    .service-card.selected .check-icon {
        opacity: 1;
        transform: scale(1);
        background: #2563eb;
        border-color: #2563eb;
    }
    .service-card .service-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .service-card.selected .service-icon {
        background: #dbeafe;
    }
    .service-card:not(.selected) .service-icon {
        background: #f1f5f9;
    }
    .service-card .service-label-text {
        color: #475569;
    }
    .service-card.selected .service-label-text {
        color: #1e40af;
    }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto" x-data="bookingForm()">
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900 flex items-center gap-1 mb-4 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Buat Pengiriman Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Isi detail pengiriman Anda di bawah ini</p>
    </div>

    <form method="POST" action="{{ route('customer.booking.store') }}" @submit="isSubmitting = true">
        @csrf

        {{-- Step 1: Route & Service --}}
        <div class="section-panel">
            <h2 class="text-base font-semibold text-slate-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                Rute & Layanan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="origin_city">Kota Asal <span class="text-red-500">*</span></label>
                    <select id="origin_city" name="origin_city" x-model="origin" @change="resetRate()" required
                            class="form-input appearance-none cursor-pointer">
                        <option value="">-- Pilih Kota Asal --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('origin_city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @error('origin_city')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="destination_city">Kota Tujuan <span class="text-red-500">*</span></label>
                    <input id="destination_city" type="text" name="destination_city" x-model="destination"
                           value="{{ old('destination_city') }}" placeholder="Contoh: Bandung" required
                           @input="resetRate()" class="form-input">
                    @error('destination_city')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="form-label">Jenis Layanan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Regular Service -->
                    <label class="relative cursor-pointer service-label" @mouseenter="hoverRegular = true" @mouseleave="hoverRegular = false">
                        <input type="radio" name="service_type" value="regular" x-model="serviceType" @change="resetRate()" class="sr-only peer" {{ old('service_type', 'regular') === 'regular' ? 'checked' : '' }}>
                        <div class="service-card" :class="{ 'selected': serviceType === 'regular' }">
                            <div class="check-icon">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="service-icon">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-slate-900 text-sm">Regular</div>
                            <div class="text-xs text-slate-500 mt-1">Estimasi 2–5 hari kerja</div>
                            <div class="text-xs text-blue-600 font-medium mt-2 opacity-0" :class="{ 'opacity-100': serviceType === 'regular' }">Terpilih ✓</div>
                        </div>
                    </label>

                    <!-- Express Service -->
                    <label class="relative cursor-pointer service-label" @mouseenter="hoverExpress = true" @mouseleave="hoverExpress = false">
                        <input type="radio" name="service_type" value="express" x-model="serviceType" @change="resetRate()" class="sr-only peer" {{ old('service_type') === 'express' ? 'checked' : '' }}>
                        <div class="service-card" :class="{ 'selected': serviceType === 'express' }">
                            <div class="check-icon">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="service-icon">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-slate-900 text-sm">Express</div>
                            <div class="text-xs text-slate-500 mt-1">Estimasi 1–2 hari kerja</div>
                            <div class="text-xs text-blue-600 font-medium mt-2 opacity-0" :class="{ 'opacity-100': serviceType === 'express' }">Terpilih ✓</div>
                        </div>
                    </label>
                </div>
                @error('service_type')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Step 2: Sender & Receiver --}}
        <div class="section-panel">
            <h2 class="text-base font-semibold text-slate-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                Data Pengirim & Penerima
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Sender --}}
                <div class="space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-black/5 pb-2">Pengirim</p>
                    <div>
                        <label class="form-label" for="sender_name">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input id="sender_name" type="text" name="sender_name" value="{{ old('sender_name') }}" required class="form-input" placeholder="Nama pengirim">
                        @error('sender_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="sender_phone">No. Telepon <span class="text-red-500">*</span></label>
                        <input id="sender_phone" type="text" name="sender_phone" value="{{ old('sender_phone') }}" required class="form-input" placeholder="08xx-xxxx-xxxx">
                        @error('sender_phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="sender_address">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="sender_address" name="sender_address" rows="3" required class="form-input resize-none" placeholder="Jl. ...">{{ old('sender_address') }}</textarea>
                        @error('sender_address')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                {{-- Receiver --}}
                <div class="space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-black/5 pb-2">Penerima</p>
                    <div>
                        <label class="form-label" for="receiver_name">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input id="receiver_name" type="text" name="receiver_name" value="{{ old('receiver_name') }}" required class="form-input" placeholder="Nama penerima">
                        @error('receiver_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="receiver_phone">No. Telepon <span class="text-red-500">*</span></label>
                        <input id="receiver_phone" type="text" name="receiver_phone" value="{{ old('receiver_phone') }}" required class="form-input" placeholder="08xx-xxxx-xxxx">
                        @error('receiver_phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="receiver_address">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="receiver_address" name="receiver_address" rows="3" required class="form-input resize-none" placeholder="Jl. ...">{{ old('receiver_address') }}</textarea>
                        @error('receiver_address')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Items --}}
        <div class="section-panel">
            <h2 class="text-base font-semibold text-slate-900 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                Daftar Barang
            </h2>

            <div class="space-y-3" id="items-list">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-black/5">
                        <div class="flex-grow grid grid-cols-3 gap-3">
                            <div>
                                <label class="form-label text-xs">Nama Barang *</label>
                                <input type="text" :name="`items[${index}][name]`" x-model="item.name" required
                                       @input="resetRate()" class="form-input" placeholder="Contoh: Buku">
                            </div>
                            <div>
                                <label class="form-label text-xs">Jumlah *</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" min="1" required
                                       @input="resetRate()" class="form-input" placeholder="1">
                            </div>
                            <div>
                                <label class="form-label text-xs">Berat (kg) *</label>
                                <input type="number" step="0.1" :name="`items[${index}][weight]`" x-model="item.weight" min="0.1" required
                                       @input="resetRate()" class="form-input" placeholder="0.5">
                            </div>
                        </div>
                        <button type="button" @click="removeItem(index)"
                                x-show="items.length > 1"
                                class="mt-6 text-red-500 hover:text-red-600 transition p-1.5 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addItem()" class="mt-4 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 transition font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Barang
            </button>

            @error('items')<p class="form-error mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Rate Estimator --}}
        <div class="section-panel">
            <h2 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">4</span>
                Estimasi Ongkir
            </h2>
            <button type="button" @click="calculateRate()"
                    :disabled="isCalculating"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition shadow-sm disabled:opacity-50">
                <svg x-show="!isCalculating" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <svg x-show="isCalculating" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="isCalculating ? 'Menghitung...' : 'Hitung Estimasi Ongkir'"></span>
            </button>

            <div x-show="rateResult !== null" x-transition class="mt-5 p-5 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Total Berat</p>
                        <p class="text-lg font-bold text-slate-900" x-text="rateResult ? rateResult.total_weight + ' kg' : '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Tarif / Kg</p>
                        <p class="text-lg font-bold text-slate-900" x-text="rateResult ? 'Rp ' + formatNum(rateResult.price_per_kg) : '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Estimasi Waktu</p>
                        <p class="text-lg font-bold text-slate-900" x-text="rateResult ? rateResult.estimated_days + ' hari' : '-'"></p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200 flex items-center justify-between">
                    <span class="text-sm text-slate-600">Total Estimasi Ongkir</span>
                    <span class="text-2xl font-bold text-blue-600" x-text="rateResult ? 'Rp ' + formatNum(rateResult.total_price) : '-'"></span>
                </div>
                <p x-show="rateResult && !rateResult.route_found" class="text-xs text-amber-600 mt-3">
                    ⚠ Rute tidak ditemukan di database. Menggunakan tarif default. Harga final ditentukan di outlet.
                </p>
            </div>

            <p x-show="rateError" x-text="rateError" class="mt-3 text-sm text-red-500"></p>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('customer.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-900 transition">Batal</a>
            <button type="submit" :disabled="isSubmitting"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-8 py-3 rounded-lg shadow-sm transition disabled:opacity-60">
                <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="isSubmitting ? 'Memproses...' : 'Konfirmasi Booking'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function bookingForm() {
    return {
        origin: '{{ old("origin_city", "") }}',
        destination: '{{ old("destination_city", "") }}',
        serviceType: '{{ old("service_type", "regular") }}',
        items: [{ name: '', quantity: 1, weight: '' }],
        rateResult: null,
        rateError: null,
        isCalculating: false,
        isSubmitting: false,

        addItem() {
            this.items.push({ name: '', quantity: 1, weight: '' });
        },
        removeItem(index) {
            this.items.splice(index, 1);
            this.resetRate();
        },
        resetRate() {
            this.rateResult = null;
            this.rateError = null;
        },
        getTotalWeight() {
            return this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.weight) || 0) * (parseInt(item.quantity) || 0);
            }, 0);
        },
        formatNum(n) {
            return new Intl.NumberFormat('id-ID').format(Math.round(n));
        },
        async calculateRate() {
            if (!this.origin || !this.destination) {
                this.rateError = 'Pilih kota asal dan isi kota tujuan terlebih dahulu.';
                return;
            }
            const totalWeight = this.getTotalWeight();
            if (totalWeight <= 0) {
                this.rateError = 'Tambahkan minimal 1 barang dengan berat yang valid.';
                return;
            }

            this.isCalculating = true;
            this.rateError = null;
            this.rateResult = null;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const resp = await fetch('{{ route("customer.calculate-rate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({
                        origin: this.origin,
                        destination: this.destination,
                        weight: totalWeight,
                        service_type: this.serviceType
                    })
                });
                const data = await resp.json();
                if (resp.ok) {
                    data.total_weight = totalWeight.toFixed(2);
                    this.rateResult = data;
                } else {
                    this.rateError = 'Gagal menghitung tarif. Coba lagi.';
                }
            } catch(e) {
                this.rateError = 'Terjadi kesalahan jaringan.';
            } finally {
                this.isCalculating = false;
            }
        }
    }
}
</script>
@endsection
