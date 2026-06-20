<?php

declare(strict_types=1);

use MyLaravelTools\Panel\Http\Controllers\LogoutController;
use MyLaravelTools\Panel\Http\Controllers\VerifyEmailController;
use MyLaravelTools\Panel\Http\Middleware\RedirectIfPanelAuthenticated;
use MyLaravelTools\Panel\Http\Middleware\SetPanelLocale;
use MyLaravelTools\Panel\Livewire\Auth\ForgotPassword;
use MyLaravelTools\Panel\Livewire\Auth\Login;
use MyLaravelTools\Panel\Livewire\Auth\Register;
use MyLaravelTools\Panel\Livewire\Auth\ResetPassword;
use MyLaravelTools\Panel\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;

if (! config('panel.auth.enabled', true)) {
    return;
}

$guard = config('panel.guard', 'web');

Route::middleware(['web', SetPanelLocale::class, RedirectIfPanelAuthenticated::class])->group(function (): void {
    Route::get('/login', Login::class)->name('login');

    if (config('panel.auth.register', true)) {
        Route::get('/register', Register::class)->name('register');
    }

    if (config('panel.auth.password_reset', true)) {
        Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    }
});

if (config('panel.auth.email_verification', false)) {
    Route::middleware(['web', SetPanelLocale::class, 'auth:' . $guard])->group(function (): void {
        Route::get('/email/verify', VerifyEmail::class)->name('verification.notice');
    });

    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['web', SetPanelLocale::class, 'signed', 'auth:' . $guard])
        ->name('verification.verify');
}

Route::post('/logout', LogoutController::class)
    ->middleware('web')
    ->name('logout');
