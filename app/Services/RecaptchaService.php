<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    protected ?string $secretKey;
    protected bool $enabled;

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key');
        $this->enabled = filter_var(config('services.recaptcha.enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (!$this->enabled) {
            return true;
        }

        if (empty($token)) {
            return empty($this->secretKey) || app()->environment('local', 'testing');
        }

        if (empty($this->secretKey)) {
            return app()->environment('local', 'testing');
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $ip
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // reCAPTCHA v2: just check success
                return isset($data['success']) && $data['success'] === true;
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA Validation Error: ' . $e->getMessage());
        }

        return false;
    }
}
