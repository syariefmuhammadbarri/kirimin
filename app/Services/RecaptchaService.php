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
        $this->secretKey = config('services.recaptcha.secret_key') ?: env('RECAPTCHA_SECRET_KEY');
        $this->enabled = filter_var(env('RECAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (!$this->enabled) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $ip
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success'] === true) {
                    $score = $data['score'] ?? 0.0;
                    // Google reCAPTCHA v3 threshold is 0.5
                    return $score >= 0.5;
                }
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA Validation Error: ' . $e->getMessage());
        }

        return false;
    }
}
