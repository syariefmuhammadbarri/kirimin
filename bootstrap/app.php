<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (dan platform PaaS lain) terminate HTTPS di reverse proxy
        // di depan container ini. Tanpa baris ini, Laravel mengira semua
        // request datang via HTTP biasa, sehingga url()/asset()/@vite
        // generate link http:// padahal halamannya dibuka via https:// —
        // browser blokir sebagai mixed content, hasilnya CSS/JS gagal load.
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'webhook/midtrans',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'recaptcha' => \App\Http\Middleware\RecaptchaMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
