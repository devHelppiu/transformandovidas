<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies (CloudFlare, load balancers) - FIX 56
        $middleware->trustProxies(at: '*');
        
        // Global security headers for all web requests
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'referral' => \App\Http\Middleware\CaptureReferralMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/helppiu',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
