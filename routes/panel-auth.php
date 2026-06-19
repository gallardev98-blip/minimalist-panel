<?php

declare(strict_types=1);

use Panel\Minimalist\Http\Controllers\LogoutController;
use Panel\Minimalist\Http\Middleware\RedirectIfPanelAuthenticated;
use Panel\Minimalist\Livewire\Auth\ForgotPassword;
use Panel\Minimalist\Livewire\Auth\Login;
use Panel\Minimalist\Livewire\Auth\Register;
use Panel\Minimalist\Livewire\Auth\ResetPassword;
use Illuminate\Support\Facades\Route;

if (! config('panel.auth.enabled', true)) {
    return;
}

Route::middleware(['web', RedirectIfPanelAuthenticated::class])->group(function (): void {
    Route::get('/login', Login::class)->name('login');

    if (config('panel.auth.register', true)) {
        Route::get('/register', Register::class)->name('register');
    }

    if (config('panel.auth.password_reset', true)) {
        Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    }
});

Route::post('/logout', LogoutController::class)
    ->middleware('web')
    ->name('logout');
