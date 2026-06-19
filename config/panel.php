<?php
declare(strict_types=1);
return [
  'path' => env('PANEL_PATH', 'admin'),
  'middleware' => [
    'web',
    Panel\Minimalist\Http\Middleware\EnsurePanelAccess::class,
  ],
  'guard' => env('PANEL_GUARD', 'web'),
  'logout_route' => env('PANEL_LOGOUT_ROUTE', 'panel.logout'),
  'login_route' => env('PANEL_LOGIN_ROUTE', 'panel.login'),
  /*
  |--------------------------------------------------------------------------
  | Autenticación integrada
  |--------------------------------------------------------------------------
  |
  | Login y registro en {panel.path}/login y /register (tabla users de Laravel).
  | Si enabled=false, usa login_route/logout_route de tu app (Breeze, Fortify…).
  |
  | register_role — rol Spatie al registrarse (requiere HasRoles en User).
  |
  */
  'auth' => [
    'enabled' => env('PANEL_AUTH_ENABLED', true),
    'register' => env('PANEL_AUTH_REGISTER', true),
    'user_model' => env('PANEL_AUTH_USER_MODEL'),
    'redirect_after_login' => env('PANEL_AUTH_REDIRECT_LOGIN'),
    'redirect_after_register' => env('PANEL_AUTH_REDIRECT_REGISTER'),
    'register_role' => env('PANEL_AUTH_REGISTER_ROLE'),
  ],
  'resources' => [],
  'discovery' => [
    'enabled' => env('PANEL_AUTO_DISCOVER', true),
    'path' => app_path('Panel/Resources'),
    'namespace' => 'App\\Panel\\Resources',
  ],
  /*
  |--------------------------------------------------------------------------
  | Páginas custom (no CRUD)
  |--------------------------------------------------------------------------
  |
  | Auto-discovery en app/Panel/Pages. Cada Page define vista, permiso y slug.
  | Ruta: {panel.path}/pages/{slug}
  |
  */
  'pages' => [
    'registered' => [],
    'discovery' => [
      'enabled' => env('PANEL_PAGES_AUTO_DISCOVER', true),
      'path' => app_path('Panel/Pages'),
      'namespace' => 'App\\Panel\\Pages',
    ],
  ],
  /*
  |--------------------------------------------------------------------------
  | Permisos (Spatie Laravel Permission o Gate)
  |--------------------------------------------------------------------------
  |
  | enabled — activa comprobaciones de permiso en middleware, Pages y navegación.
  | driver  — spatie (HasRoles) o gate (Laravel Gate).
  | panel_access — permiso mínimo para entrar al panel (EnsurePanelAccess).
  |
  | Requiere en el host: composer require spatie/laravel-permission (opcional).
  |
  */
  'permissions' => [
    'enabled' => env('PANEL_PERMISSIONS_ENABLED', false),
    'driver' => env('PANEL_PERMISSIONS_DRIVER', 'spatie'),
    'panel_access' => env('PANEL_PERMISSION_ACCESS', 'access panel'),
  ],
  'navigation' => null,
  'navigation_groups_expanded' => env('PANEL_NAV_GROUPS_EXPANDED', false),
  'brand' => [
    'name' => env('PANEL_BRAND_NAME', 'Panel'),
    'logo' => env('PANEL_BRAND_LOGO'),
  ],
  'per_page' => 15,
  /*
  |--------------------------------------------------------------------------
  | Formularios en modal
  |--------------------------------------------------------------------------
  |
  | Si es true, crear y editar se abren en un modal sobre el listado.
  | Si es false, se usan las rutas de página completa (panel.resources.create/edit).
  |
  */
  'forms_in_modal' => env('PANEL_FORMS_IN_MODAL', true),
  /*
  |--------------------------------------------------------------------------
  | Policies de Laravel
  |--------------------------------------------------------------------------
  |
  | auto_register — registra Gate::policy() para cada Resource al arrancar.
  | namespace   — carpeta donde buscar {Model}Policy (convención Laravel).
  |
  | Orden de autorización: hooks can*() del Resource AND Policy (si existe).
  |
  */
  'policies' => [
    'auto_register' => env('PANEL_POLICIES_AUTO_REGISTER', true),
    'namespace' => env('PANEL_POLICIES_NAMESPACE', 'App\\Policies'),
  ],
  /*
  |--------------------------------------------------------------------------
  | Tema y colores
  |--------------------------------------------------------------------------
  |
  | Paleta monocromática por defecto (blanco/negro). Personaliza en hex o rgb().
  | Modo claro: :root | Modo oscuro: .dark (html.dark)
  |
  | primary      → botones, links activos, acentos
  | primary_dark → primary en modo oscuro (por defecto blanco)
  | light/dark   → fondos, bordes, textos por modo
  |
  */
  'theme' => [
    'default' => env('PANEL_THEME', 'dark'),
    'font' => env('PANEL_FONT', 'Plus Jakarta Sans'),
    'radius' => '0.75rem',
    'sidebar_width' => '16rem',
    'colors' => [
      'primary' => env('PANEL_COLOR_PRIMARY', '#000000'),
      'primary_hover' => env('PANEL_COLOR_PRIMARY_HOVER', '#262626'),
      'primary_dark' => env('PANEL_COLOR_PRIMARY_DARK', '#ffffff'),
      'primary_hover_dark' => env('PANEL_COLOR_PRIMARY_HOVER_DARK', '#e5e5e5'),
      'accent' => env('PANEL_COLOR_ACCENT', '#525252'),
      'accent_dark' => env('PANEL_COLOR_ACCENT_DARK', '#a3a3a3'),
      'success' => env('PANEL_COLOR_SUCCESS', '#16a34a'),
      'danger' => env('PANEL_COLOR_DANGER', '#dc2626'),
      'warning' => env('PANEL_COLOR_WARNING', '#ca8a04'),
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
  'widgets' => [
    // Panel\Minimalist\Widgets\ResourceCountWidget::make(App\Panel\Resources\ProductResource::class),
  ],
];
