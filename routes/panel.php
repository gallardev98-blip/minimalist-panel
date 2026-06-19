<?php

declare(strict_types=1);

use Panel\Minimalist\Livewire\Dashboard;
use Panel\Minimalist\Livewire\PanelPage;
use Panel\Minimalist\Livewire\ResourceForm;
use Panel\Minimalist\Livewire\ResourceIndex;
use Panel\Minimalist\Livewire\ResourceShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');

Route::get('/pages/{page}', PanelPage::class)->name('pages.show');

Route::prefix('resources/{resource}')->group(function (): void {
    Route::get('/', ResourceIndex::class)->name('resources.index');
    Route::get('/create', ResourceForm::class)->name('resources.create');
    Route::get('/{record}', ResourceShow::class)->name('resources.show');
    Route::get('/{record}/edit', ResourceForm::class)->name('resources.edit');
});
