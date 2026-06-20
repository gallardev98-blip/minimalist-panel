<?php

declare(strict_types=1);

use MyLaravelTools\Panel\Livewire\Dashboard;
use MyLaravelTools\Panel\Livewire\PanelPage;
use MyLaravelTools\Panel\Livewire\ResourceForm;
use MyLaravelTools\Panel\Livewire\ResourceIndex;
use MyLaravelTools\Panel\Livewire\ResourceShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');

if (config('panel.profile.enabled', true)) {
    Route::get('/profile', \MyLaravelTools\Panel\Livewire\Profile::class)->name('profile');
}

Route::get('/pages/{page}', PanelPage::class)->name('pages.show');

Route::prefix('resources/{resource}')->group(function (): void {
    Route::get('/', ResourceIndex::class)->name('resources.index');
    Route::get('/create', ResourceForm::class)->name('resources.create');
    Route::get('/{record}', ResourceShow::class)->name('resources.show');
    Route::get('/{record}/edit', ResourceForm::class)->name('resources.edit');
});
