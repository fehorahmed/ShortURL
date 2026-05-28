<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShortLinkController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\PlanController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/shorten', [ShortLinkController::class, 'store'])->name('links.shorten');

// ── Auth (Breeze) ────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Authenticated User ───────────────────────────────────────
Route::middleware(['auth', 'check_active'])->group(function () {

    Route::prefix('dashboard')->name('user.')->group(function () {
        Route::get('/',      [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/links', [DashboardController::class, 'links'])->name('links');
        Route::delete('/links/{shortLink}', [DashboardController::class, 'deleteLink'])->name('links.destroy');

        Route::get('/plans',                    [PlanController::class, 'index'])->name('plans');
        Route::post('/plans/{plan}/purchase',   [PlanController::class, 'purchase'])->name('plans.purchase');
        Route::get('/payment/success',          [PlanController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/fail',             [PlanController::class, 'paymentFail'])->name('payment.fail');
    });

});

// ── Admin Panel ──────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('users',                         [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}',                  [Admin\UserController::class, 'show'])->name('users.show');
    Route::delete('users/{user}',               [Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/toggle',          [Admin\UserController::class, 'toggleActive'])->name('users.toggle');
    Route::post('users/{user}/assign-plan',     [Admin\UserController::class, 'assignPlan'])->name('users.assign-plan');

    // Plans
    Route::get('plans',                         [Admin\PlanController::class, 'index'])->name('plans.index');
    Route::post('plans',                        [Admin\PlanController::class, 'store'])->name('plans.store');
    Route::put('plans/{plan}',                  [Admin\PlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}',               [Admin\PlanController::class, 'destroy'])->name('plans.destroy');

    // Advertisements
    Route::get('ads',                           [Admin\AdvertisementController::class, 'index'])->name('ads.index');
    Route::post('ads',                          [Admin\AdvertisementController::class, 'store'])->name('ads.store');
    Route::put('ads/{advertisement}',           [Admin\AdvertisementController::class, 'update'])->name('ads.update');
    Route::delete('ads/{advertisement}',        [Admin\AdvertisementController::class, 'destroy'])->name('ads.destroy');
    Route::post('ads/{advertisement}/toggle',   [Admin\AdvertisementController::class, 'toggle'])->name('ads.toggle');

    // Links
    Route::get('links',                         [Admin\LinkController::class, 'index'])->name('links.index');
    Route::delete('links/{shortLink}',          [Admin\LinkController::class, 'destroy'])->name('links.destroy');

    // Payments
    Route::get('payments',                      [Admin\PaymentController::class, 'index'])->name('payments.index');

    // Settings
    Route::get('settings',                      [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings',                     [Admin\SettingsController::class, 'update'])->name('settings.update');

});

// ── Short URL Redirect (must be LAST) ───────────────────────
Route::get('/{code}', [ShortLinkController::class, 'redirect'])
    ->where('code', '[a-zA-Z0-9_-]+')
    ->name('link.redirect');


// Command
Route::get('/command/migrate', function () {
    Artisan::call('migrate:fresh --seed');
    return 'Command executed!';
});
Route::get('/command/seed', function () {
    Artisan::call('db:seed');
    return 'Command executed!';
});
// clear all
Route::get('/command/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Cache cleared!';
});