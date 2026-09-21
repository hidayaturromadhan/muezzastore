<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Alias middleware custom
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        /**
         * CSRF Exclusion (Webhook)
         *
         * Catatan:
         * - Route yang kita buat sebenarnya: /midtrans/webhook dan /digiflazz/webhook
         * - Tapi karena kamu jalan di XAMPP + subfolder /muezza_shop/public,
         *   request bisa datang dengan prefix tersebut.
         *
         * Jadi kita amankan pakai wildcard.
         */
        $middleware->validateCsrfTokens(except: [
            // Midtrans
            'midtrans/webhook',
            '*midtrans/webhook*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
