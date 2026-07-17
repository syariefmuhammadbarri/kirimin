<?php

namespace Tests\Unit;

use App\Services\RecaptchaService;
use Tests\TestCase;

class RecaptchaServiceTest extends TestCase
{
    public function test_local_environment_accepts_missing_recaptcha_token_for_demo_flow(): void
    {
        config(['services.recaptcha.enabled' => true]);
        config(['services.recaptcha.secret_key' => '']);

        $service = new RecaptchaService();

        $this->assertTrue($service->verify(null));
    }
}
