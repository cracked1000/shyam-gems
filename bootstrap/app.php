<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // Add this line
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'seller' => \App\Http\Middleware\SellerMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            '2fa.verified' => \App\Http\Middleware\Ensure2FAIsVerified::class, // Add 2FA verification middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();