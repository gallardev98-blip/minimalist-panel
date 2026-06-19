<?php

declare(strict_types=1);

use Panel\Minimalist\Http\Controllers\LogoutController;
use Panel\Minimalist\Http\Middleware\RedirectIfPanelAuthenticated;
use Panel\Minimalist\Livewire\Auth\Login;
use Panel\Minimalist\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

if (! config('panel.auth.enabled', true)) {
    return;
}

Route::middleware(['web', RedirectIfPanelAuthenticated::class])->group(function (): void {
    Route::get('/login', Login::class)->name('login');

    if (config('panel.auth.register', true)) {
        Route::get('/register', Register::class)->name('register');
    }
});

Route::post('/logout', LogoutController::class)
    ->middleware('web')
    ->name('logout');
