<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\PwaManifestController;
use App\Http\Controllers\Tenant\ServiceWorkerController;
use App\Http\Controllers\Tenant\StaffAuthController;
use App\Http\Controllers\Tenant\StaffDashboardController;
use App\Http\Controllers\Tenant\UserAuthController;
use App\Http\Controllers\Tenant\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Dynamic PWA Manifest (strict {portal} in ['staff', 'users'])
    Route::get('{portal}/manifest.webmanifest', PwaManifestController::class)
        ->whereIn('portal', ['staff', 'users'])
        ->name('pwa.manifest');

    // Root Service Worker
    Route::get('sw.js', ServiceWorkerController::class)->name('pwa.sw');

    // Tenant Root & Scope Redirects
    Route::get('/', function () {
        if (auth()->guard('staff')->check()) {
            return redirect()->route('tenant.staff.dashboard');
        }

        if (auth()->guard('web')->check()) {
            return redirect()->route('tenant.users.dashboard');
        }

        return redirect()->route('tenant.staff.login');
    })->name('tenant.home');

    Route::get('staff', function () {
        return auth()->guard('staff')->check()
            ? redirect()->route('tenant.staff.dashboard')
            : redirect()->route('tenant.staff.login');
    })->name('tenant.staff.home');

    Route::get('users', function () {
        return auth()->guard('web')->check()
            ? redirect()->route('tenant.users.dashboard')
            : redirect()->route('tenant.users.login');
    })->name('tenant.users.home');

    // Portal A: Staff & Teachers (/staff/*)
    Route::prefix('staff')->group(function () {
        Route::middleware('guest:staff')->group(function () {
            Route::get('login', [StaffAuthController::class, 'showLogin'])->name('tenant.staff.login');
            Route::post('login', [StaffAuthController::class, 'login'])->name('tenant.staff.login.store');
        });

        Route::middleware('auth:staff')->group(function () {
            Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('tenant.staff.dashboard');
            Route::post('logout', [StaffAuthController::class, 'logout'])->name('tenant.staff.logout');
        });
    });

    // Portal B: Students & Parents (/users/*)
    Route::prefix('users')->group(function () {
        Route::middleware('guest:web')->group(function () {
            Route::get('login', [UserAuthController::class, 'showLogin'])->name('tenant.users.login');
            Route::post('login', [UserAuthController::class, 'login'])->name('tenant.users.login.store');
        });

        Route::middleware('auth:web')->group(function () {
            Route::get('dashboard', [UserDashboardController::class, 'index'])->name('tenant.users.dashboard');
            Route::post('logout', [UserAuthController::class, 'logout'])->name('tenant.users.logout');
        });
    });

    // Backward-compatibility aliases
    Route::get('login', fn () => redirect()->route('tenant.staff.login'))->name('tenant.login');
    Route::get('dashboard', fn () => redirect()->route('tenant.staff.dashboard'))->name('tenant.dashboard');
    Route::post('logout', [StaffAuthController::class, 'logout'])->name('tenant.logout');
});
