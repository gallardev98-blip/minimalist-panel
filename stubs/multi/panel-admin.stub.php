<?php

declare(strict_types=1);

/*
| Panel administración — config de un panel en modo multi-panel.
*/

return [
    'path' => 'admin',
    'locale' => 'es',
    'locales' => ['es' => 'Español', 'en' => 'English'],
    'locale_selector' => true,
    'middleware' => [
        'web',
        MyLaravelTools\Panel\Http\Middleware\SetPanelLocale::class,
        MyLaravelTools\Panel\Http\Middleware\EnsurePanelAccess::class,
        MyLaravelTools\Panel\Http\Middleware\EnsurePanelEmailVerified::class,
    ],
    'guard' => 'web',
    'auth' => [
        'enabled' => true,
        'register' => true,
    ],
    'profile' => ['enabled' => true],
    'brand' => [
        'name' => 'Admin',
        'tagline' => null,
    ],
    'resources' => [],
    'widgets' => [],
    'navigation' => null,
];
