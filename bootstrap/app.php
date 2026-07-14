<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckUserActive;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases — Laravel 13 way (no Kernel.php)
        $middleware->alias([
            'admin'        => AdminMiddleware::class,
            'check_active' => CheckUserActive::class,
        ]);
        // Exclude SSLCommerz webhook/callback routes from CSRF validation
        $middleware->validateCsrfTokens(except: [
            'sslcommerz/*',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('links:clean-expired')->hourly();
        $schedule->command('subscriptions:expire')->hourly();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
