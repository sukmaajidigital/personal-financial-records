<?php

use App\Http\Controllers\Auth\EmailVerificationCodeController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\TrackSiteView;
use App\Models\SiteView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    // Cache counts for 60 seconds to reduce database queries
    $stats = Cache::remember('site_stats', 60, function () {
        return [
            'totalViews' => SiteView::totalUniqueVisitors(),
            'totalRegistered' => User::count(),
        ];
    });

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'stats' => $stats,
    ]);
})->middleware(TrackSiteView::class)->name('home');

// Google OAuth routes
Route::middleware('guest')->group(function () {
    Route::get('auth/google/redirect', [SocialiteController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('auth/google/callback', [SocialiteController::class, 'callback'])->name('auth.google.callback');
});

// Email verification code routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify-code', [EmailVerificationCodeController::class, 'show'])->name('verification.code.show');
    Route::post('email/verify-code', [EmailVerificationCodeController::class, 'verify'])->name('verification.code.verify');
    Route::post('email/resend-code', [EmailVerificationCodeController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.code.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

require __DIR__ . '/settings.php';
