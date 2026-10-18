<?php

use Illuminate\Support\Facades\Route;

$centralDomain = config('tenancy.central_domains.0', 'localhost');

Route::domain($centralDomain)->group(function () {
    Route::inertia('/', 'Welcome')->name('home');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    });

    require __DIR__.'/settings.php';
});
