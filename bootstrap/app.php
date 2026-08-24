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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Percaya header dari reverse proxy (ngrok, nginx, load balancer, dll)
        // supaya Laravel tahu request aslinya HTTPS meskipun diteruskan via HTTP
        // secara internal. Tanpa ini, Laravel bikin redirect/url pakai http://
        // yang diblokir browser sebagai "Mixed Content" saat app diakses via https.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Endpoint webhook Midtrans dikecualikan dari verifikasi CSRF karena
        // request datang langsung dari server Midtrans, bukan dari browser.
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();