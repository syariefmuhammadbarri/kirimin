<?php

namespace App\Http\Middleware;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaMiddleware
{
    protected RecaptchaService $recaptcha;

    public function __construct(RecaptchaService $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (filter_var(config('services.recaptcha.enabled', false), FILTER_VALIDATE_BOOLEAN)) {
            $token = $request->input('g-recaptcha-response');
            
            if (!$this->recaptcha->verify($token, $request->ip())) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'reCAPTCHA verification failed. Bot detected.',
                        'errors' => [
                            'recaptcha' => ['reCAPTCHA validation failed. Please try again.']
                        ]
                    ], 422);
                }

                return redirect()->back()->withErrors([
                    'recaptcha' => 'reCAPTCHA verification failed. Please try again.'
                ])->withInput();
            }
        }

        return $next($request);
    }
}
