<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\TenantAuthController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('tenant.dashboard')
            : redirect()->route('tenant.login');
    })->name('tenant.home');

    Route::middleware('guest')->group(function () {
        Route::get('login', [TenantAuthController::class, 'showLogin'])->name('tenant.login');
        Route::post('login', [TenantAuthController::class, 'login'])->name('tenant.login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');
        Route::post('logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');
    });
});
