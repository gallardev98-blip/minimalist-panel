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

    /*
    |--------------------------------------------------------------------------
    | Idioma del panel
    |--------------------------------------------------------------------------
    |
    | null — usa el locale de la app (config/app.php).
    | 'es' / 'en' — fuerza el idioma en rutas del panel y auth.
    |
    */

    'locale' => 'es',

    /*
    |--------------------------------------------------------------------------
    | Idiomas disponibles (selector en sidebar)
    |--------------------------------------------------------------------------
    |
    | Clave => etiqueta visible. Con más de un idioma y locale_selector=true
    | aparece un selector en el footer del sidebar.
    |
    */

    'locales' => [
        'es' => 'Español',
        'en' => 'English',
    ],

    'locale_selector' => true,

    'middleware' => [
        'web',
        MyLaravelTools\Panel\Http\Middleware\SetPanelLocale::class,
        MyLaravelTools\Panel\Http\Middleware\EnsurePanelAccess::class,
        MyLaravelTools\Panel\Http\Middleware\EnsurePanelEmailVerified::class,
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
        'email_verification' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Perfil de usuario
    |--------------------------------------------------------------------------
    |
    | Ruta: {path}/profile — el usuario logueado edita nombre, email y contraseña.
    |
    */

    'profile' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Suplantación de usuario (impersonation)
    |--------------------------------------------------------------------------
    |
    | Permite a un admin navegar el panel como otro usuario (permisos, menú, policies).
    | RowAction "Entrar como" en el resource del modelo User.
    | permission — Spatie/Gate; solo usuarios con ese permiso pueden suplantar.
    | exclude_ids — IDs que nunca se pueden suplantar.
    |
    */

    'impersonation' => [
        'enabled' => false,
        'permission' => 'impersonate users',
        'exclude_ids' => [],
        'banner' => true,
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
        /*
        | Recursos integrados Role/Permission cuando Spatie está instalado.
        | Si defines RoleResource o PermissionResource en app/Panel/Resources
        | con el mismo slug (roles / permissions), prevalece el tuyo.
        */
        'resources' => true,
        'manage_permission' => 'manage users',
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

    /*
    |--------------------------------------------------------------------------
    | Versión mostrada en el sidebar
    |--------------------------------------------------------------------------
    |
    | null — usa la versión del paquete (Package::VERSION) con prefijo "v".
    | string — texto libre, p. ej. "v1" o "beta".
    |
    */

    'version' => null,

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
    | Importación CSV / Excel
    |--------------------------------------------------------------------------
    |
    | Botón "Importar" en listados de resources (requiere permiso create).
    | Columnas: cabeceras del archivo = label o name de los fields del form().
    |
    */

    'import' => [
        'enabled' => true,
        'preview' => true,
    ],

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
    | MyLaravelTools\Panel\Widgets\ResourceCountWidget::make(App\Panel\Resources\ProductResource::class),
    |
    */

    'widgets' => [],

    /*
    |--------------------------------------------------------------------------
    | Integraciones opcionales
    |--------------------------------------------------------------------------
    |
    | alertas — mylaraveltools/alertas (toast/modal en auth y layouts del panel).
    | Requiere composer require mylaraveltools/alertas y @include en layout (automático).
    |
    */

    'integrations' => [
        'alertas' => true,
    ],

];
