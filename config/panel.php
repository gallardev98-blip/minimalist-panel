<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Minimalist Panel — configuración
|--------------------------------------------------------------------------
|
| Edita este archivo tras `php artisan panel:install`.
| Toda la configuración vive aquí; no hace falta usar variables .env.
|
| Si prefieres .env, puedes envolver cualquier valor, por ejemplo:
|   'path' => env('PANEL_PATH', 'admin'),
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Ruta y acceso
    |--------------------------------------------------------------------------
    */

    'path' => 'admin',

    'middleware' => [
        'web',
        Panel\Minimalist\Http\Middleware\EnsurePanelAccess::class,
    ],

    'guard' => 'web',

    'logout_route' => 'panel.logout',

    'login_route' => 'panel.login',

    /*
    |--------------------------------------------------------------------------
    | Autenticación integrada
    |--------------------------------------------------------------------------
    |
    | Login y registro en {path}/login y /register (tabla users de Laravel).
    | Con enabled=false, usa login_route/logout_route de tu app (Breeze, Fortify…).
    |
    | user_model — null usa config('auth.providers.users.model').
    | register_role — rol Spatie al registrarse (requiere HasRoles en User).
    | redirect_after_* — null usa la ruta del dashboard del panel.
    |
    */

    'auth' => [
        'enabled' => true,
        'register' => true,
        'user_model' => null,
        'redirect_after_login' => null,
        'redirect_after_register' => null,
        'register_role' => null,
        'password_reset' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources (CRUD)
    |--------------------------------------------------------------------------
    */

    'resources' => [],

    'discovery' => [
        'enabled' => true,
        'path' => app_path('Panel/Resources'),
        'namespace' => 'App\\Panel\\Resources',
    ],

    /*
    |--------------------------------------------------------------------------
    | Páginas custom (no CRUD)
    |--------------------------------------------------------------------------
    |
    | Auto-discovery en app/Panel/Pages. Ruta: {path}/pages/{slug}
    |
    */

    'pages' => [
        'registered' => [],
        'discovery' => [
            'enabled' => true,
            'path' => app_path('Panel/Pages'),
            'namespace' => 'App\\Panel\\Pages',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permisos (Spatie Laravel Permission o Gate)
    |--------------------------------------------------------------------------
    |
    | Requiere en el host: composer require spatie/laravel-permission (opcional).
    |
    */

    'permissions' => [
        'enabled' => false,
        'driver' => 'spatie',
        'panel_access' => 'access panel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navegación
    |--------------------------------------------------------------------------
    |
    | null — menú generado automáticamente desde Resources y Pages.
    | array — enlaces manuales (ver README). También: require __DIR__.'/panel-navigation.php'
    |
    */

    'navigation' => null,

    'navigation_groups_expanded' => false,

    /*
    |--------------------------------------------------------------------------
    | Marca
    |--------------------------------------------------------------------------
    */

    'brand' => [
        'name' => 'Panel',
        'logo' => null,
    ],

    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Formularios en modal
    |--------------------------------------------------------------------------
    |
    | true — crear/editar en modal sobre el listado.
    | false — rutas de página completa (panel.resources.create/edit).
    |
    */

    'forms_in_modal' => true,

    /*
    |--------------------------------------------------------------------------
    | Policies de Laravel
    |--------------------------------------------------------------------------
    |
    | auto_register — registra Gate::policy() para cada Resource al arrancar.
    | Orden de autorización: hooks can*() del Resource AND Policy (si existe).
    |
    */

    'policies' => [
        'auto_register' => true,
        'namespace' => 'App\\Policies',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tema y colores
    |--------------------------------------------------------------------------
    |
    | Paleta monocromática por defecto. Modo claro: :root | oscuro: .dark (html.dark)
    |
    */

    'theme' => [
        'default' => 'dark',
        'font' => 'Plus Jakarta Sans',
        'radius' => '0.75rem',
        'sidebar_width' => '16rem',
        'colors' => [
            'primary' => '#000000',
            'primary_hover' => '#262626',
            'primary_dark' => '#ffffff',
            'primary_hover_dark' => '#e5e5e5',
            'accent' => '#525252',
            'accent_dark' => '#a3a3a3',
            'success' => '#16a34a',
            'danger' => '#dc2626',
            'warning' => '#ca8a04',
        ],
        'light' => [
            'bg' => '#ffffff',
            'surface' => '#fafafa',
            'card' => '#ffffff',
            'elevated' => '#f5f5f5',
            'border' => '#e5e5e5',
            'heading' => '#0a0a0a',
            'text' => '#404040',
            'muted' => '#737373',
            'input_bg' => '#ffffff',
            'input_border' => '#d4d4d4',
        ],
        'dark' => [
            'bg' => '#0a0a0a',
            'surface' => '#111111',
            'card' => '#141414',
            'elevated' => '#1a1a1a',
            'border' => '#262626',
            'heading' => '#fafafa',
            'text' => '#d4d4d4',
            'muted' => '#737373',
            'input_bg' => '#0a0a0a',
            'input_border' => '#404040',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets del dashboard
    |--------------------------------------------------------------------------
    |
    | Panel\Minimalist\Widgets\ResourceCountWidget::make(App\Panel\Resources\ProductResource::class),
    |
    */

    'widgets' => [],

];
