@extends('layouts.app')

@section('styles')
<style>
    .otp-input {
        width: 54px;
        height: 64px;
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s;
        color: #1e293b;
        background: #f8fafc;
    }
    .otp-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        background: #ffffff;
    }
    .otp-input.filled {
        border-color: #3b82f6;
        background: #eff6ff;
    }
</style>
@endsection

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] py-8">
    <div class="w-full max-w-xl p-8 rounded-2xl glass-panel shadow-2xl border border-slate-800/80">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Verifikasi Email</h1>
            <p class="text-sm text-slate-600">Masukkan kode OTP 6 digit yang telah dikirim ke email Anda.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-950/40 border border-emerald-800 text-emerald-300 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-6 p-6 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700">
            <p class="text-sm leading-7">
                Kami telah mengirimkan kode verifikasi ke <strong>{{ Auth::user()->email }}</strong>.
                Periksa inbox atau folder spam Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.verify-otp') }}" class="space-y-6" id="otp-form">
            @csrf

            <!-- OTP Input Fields -->
            <div class="flex justify-center gap-3" id="otp-container">
                <input type="text" name="otp_digit_1" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" autofocus required>
                <input type="text" name="otp_digit_2" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" required>
                <input type="text" name="otp_digit_3" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" required>
                <input type="text" name="otp_digit_4" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" required>
                <input type="text" name="otp_digit_5" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" required>
                <input type="text" name="otp_digit_6" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" required>
            </div>
            <input type="hidden" name="otp" id="otp-hidden">

            @error('otp')
                <p class="text-xs text-red-400 text-center">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-150">
                Verifikasi Akun
            </button>
        </form>

        <!-- Resend OTP -->
        <form method="POST" action="{{ route('verification.resend-otp') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full py-2.5 text-sm text-blue-600 hover:text-blue-500 font-medium transition">
                Kirim ulang kode verifikasi
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-600">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="font-medium text-blue-600 hover:text-blue-500 transition">
                Keluar
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-input');
    const hiddenOtp = document.getElementById('otp-hidden');

    inputs.forEach((input, index) => {
        // Auto-advance to next field
        input.addEventListener('input', function(e) {
            if (this.value.length === 1) {
                this.classList.add('filled');
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
            updateOtpValue();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].classList.remove('filled');
            }
            updateOtpValue();
        });

        // Allow only digits
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Select on focus
        input.addEventListener('focus', function() {
            this.select();
        });
    });

    function updateOtpValue() {
        let otp = '';
        inputs.forEach(input => { otp += input.value; });
        hiddenOtp.value = otp;
    }

    // Form submit validation
    document.getElementById('otp-form').addEventListener('submit', function(e) {
        let otp = '';
        inputs.forEach(input => { otp += input.value; });
        if (otp.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan kode OTP 6 digit lengkap.');
        }
    });
});
</script>
@endsection