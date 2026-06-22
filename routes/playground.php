<?php

declare(strict_types=1);

use MyLaravelTools\Panel\Livewire\PlaygroundApp;
use Illuminate\Support\Facades\Route;

$path = trim((string) config('panel.documentation.path', 'playground'), '/');

if ($path === '' || ! config('panel.documentation.enabled', true)) {
    return;
}

Route::middleware(config('panel.documentation.middleware', ['web']))
    ->get($path, PlaygroundApp::class)
    ->name('panel.playground');
