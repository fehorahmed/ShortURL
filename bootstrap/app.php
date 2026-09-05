<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckUserActive;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'        => AdminMiddleware::class,
            'check_active' => CheckUserActive::class,
        ]);

        // Exclude SSLCommerz callback routes from CSRF verification
        // SSLCommerz POSTs from their servers — no CSRF token
        $middleware->validateCsrfTokens(except: [
            'sslcommerz/success',
            'sslcommerz/failure',
            'sslcommerz/cancel',
            'sslcommerz/ipn',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('links:clean-expired')->hourly();
        $schedule->command('subscriptions:expire')->hourly();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
