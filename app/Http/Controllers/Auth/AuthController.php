<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Step 1: Show role selection page (Staff vs Customer).
     */
    public function showLoginChoice()
    {
        return view('auth.login-choose');
    }

    /**
     * Step 2: Show login form for the selected type.
     */
    public function showLoginForm(string $type)
    {
        if (!in_array($type, ['staff', 'customer'])) {
            return redirect()->route('login.choose');
        }
        return view('auth.login', ['loginType' => $type]);
    }

    /**
     * Step 3: Process login with role validation.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'login_type' => ['required', 'in:staff,customer'],
        ]);

        $loginType = $validated['login_type'];
        $credentials = [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // FR-09: Staff nonaktif tidak boleh login
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('login.form', $loginType)->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh administrator. Hubungi manager untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            // FR-10: Customer yang di-suspend tidak boleh login
            if ($user->hasRole('customer')) {
                $customer = $user->customer ?? null;
                if ($customer && $customer->is_suspended) {
                    Auth::logout();
                    $request->session()->invalidate();
                    return redirect()->route('login.form', $loginType)->withErrors([
                        'email' => 'Akun customer Anda telah ditangguhkan. Hubungi customer service untuk informasi lebih lanjut.',
                    ])->onlyInput('email');
                }
            }

            // Validate login_type matches user role
            $isStaff = $user->hasAnyRole(['manager', 'owner', 'admin_cabang', 'kurir']);
            $isCustomer = $user->hasRole('customer');

            if ($loginType === 'staff' && !$isStaff) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('login.form', 'staff')->withErrors([
                    'email' => 'Akun ini bukan akun staff. Silakan pilih "Customer" untuk login.',
                ])->onlyInput('email');
            }

            if ($loginType === 'customer' && !$isCustomer) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('login.form', 'customer')->withErrors([
                    'email' => 'Akun ini bukan akun customer. Silakan pilih "Staff" untuk login.',
                ])->onlyInput('email');
            }

            return redirect()->intended($this->getRedirectPathForUser($user));
        }

        return redirect()->route('login.form', $loginType)->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Step 1: Show role selection page for registration (Staff vs Customer).
     */
    public function showRegisterChoice()
    {
        return view('auth.register-choose');
    }

    /**
     * Step 2: Show registration form for the selected type.
     */
    public function showRegisterForm(string $type)
    {
        if (!in_array($type, ['staff', 'customer'])) {
            return redirect()->route('register.choose');
        }
        return view('auth.register', ['registerType' => $type]);
    }

    /**
     * Step 3: Process registration with role assignment.
     * Simplified: only name, email, password (no phone/address/city).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'register_type' => ['required', 'in:staff,customer'],
        ]);

        $registerType = $request->register_type;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($registerType === 'customer') {
            // Assign Spatie Role 'customer'
            $user->assignRole('customer');

            // Create minimal Customer profile (phone/address/city will be filled later via Profile page)
            Customer::create([
                'user_id' => $user->id,
                'phone' => '',
                'address' => '',
                'city' => '',
            ]);
        } else {
            // Staff registration — assign default 'kurir' role (manager can change later)
            $user->assignRole('kurir');
        }

        // Generate OTP verification code
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->verification_code = Hash::make($otp);
        $user->verification_code_expires_at = now()->addMinutes(30);
        $user->save();

        // Send OTP via email
        try {
            Mail::send('emails.verification-otp', [
                'name' => $user->name,
                'otp' => $otp,
                'email' => $user->email,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode Verifikasi Akun BAZMA Express');
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send OTP email to ' . $user->email . ': ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    public function verificationNotice()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.verify-email');
    }

    /**
     * Verify email using OTP code instead of email link.
     */
    public function verifyWithOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        // Check if OTP is expired
        if (!$user->verification_code_expires_at || now()->gt($user->verification_code_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang kode baru.',
            ]);
        }

        // Verify OTP
        if (!Hash::check($request->otp, $user->verification_code)) {
            return back()->withErrors([
                'otp' => 'Kode verifikasi yang Anda masukkan salah.',
            ]);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        // Also mark customer profile as verified
        $customer = Customer::where('user_id', $user->id)->first();
        if ($customer) {
            $customer->update(['email_verified_at' => now()]);
        }

        return redirect()->intended($this->getRedirectPathForUser($user))->with('verified', true);
    }

    /**
     * Resend OTP verification code.
     */
    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->verification_code = Hash::make($otp);
        $user->verification_code_expires_at = now()->addMinutes(30);
        $user->save();

        // Send OTP via email
        try {
            Mail::send('emails.verification-otp', [
                'name' => $user->name,
                'otp' => $otp,
                'email' => $user->email,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode Verifikasi Akun BAZMA Express');
            });
        } catch (\Exception $e) {
            Log::warning('Failed to resend OTP email to ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('status', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }

    /**
     * Dynamic redirection path depending on user role
     */
    protected function getRedirectPathForUser($user): string
    {
        if ($user->hasRole('manager')) {
            return route('manager.dashboard');
        }
        if ($user->hasRole('owner')) {
            return route('owner.dashboard');
        }
        if ($user->hasRole('admin_cabang')) {
            return route('branch.dashboard');
        }
        if ($user->hasRole('kurir')) {
            return route('courier.dashboard');
        }
        return route('customer.dashboard');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.choose')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}