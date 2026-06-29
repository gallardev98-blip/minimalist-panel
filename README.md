# Panel (`mylaraveltools/panel`)

[![Packagist Version](https://img.shields.io/packagist/v/mylaraveltools/panel)](https://packagist.org/packages/mylaraveltools/panel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/mylaraveltools/panel)](https://packagist.org/packages/mylaraveltools/panel)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

Panel de administración declarativo y monocromático para **Laravel**. API de **Resources** al estilo Filament/Nova, con **Livewire 3**, **Tailwind CSS**, auth integrada, permisos Spatie, import/export y navegación SPA.

Parte del ecosistema **[My Laravel Tools](https://packagist.org/packages/mylaraveltools/)** (junto con `mylaraveltools/alertas`).

```bash
composer require mylaraveltools/panel
```

> **Migración:** si usabas `mylaraveltools/minimalist`, sustituye por `mylaraveltools/panel`. El namespace PHP sigue siendo `MyLaravelTools\Panel` — no cambia tu código.

---

## Tabla de contenidos

1. [Requisitos](#requisitos)
2. [Instalación paso a paso](#instalación-paso-a-paso)
3. [Tu primer CRUD en 5 minutos](#tu-primer-crud-en-5-minutos)
4. [Configuración (`config/panel.php`)](#configuración-configpanelphp)
5. [Autenticación y perfil](#autenticación-y-perfil)
6. [Permisos (Spatie) y suplantación](#permisos-spatie-y-suplantación)
7. [Navegación y páginas custom](#navegación-y-páginas-custom)
8. [Importar y exportar datos](#importar-y-exportar-datos)
9. [Dashboard y widgets](#dashboard-y-widgets)
10. [Relaciones entre modelos](#relaciones-entre-modelos)
11. [Tema, layout y SPA](#tema-layout-y-spa)
12. [Comandos Artisan](#comandos-artisan)
13. [Personalizar vistas](#personalizar-vistas)
14. [Solución de problemas](#solución-de-problemas)
15. [Proyecto demo](#proyecto-demo)
16. [Desarrollo y tests](#desarrollo-y-tests)
17. [Roadmap](#roadmap)
18. [Licencia](#licencia)

---

## Requisitos

| Requisito | Versión |
|-----------|---------|
| PHP | 8.2+ |
| Laravel | 11, 12 o 13 |
| Livewire | 3.5+ |
| Tailwind CSS | 3+ en la app host |

Opcionales: `spatie/laravel-permission`, `mylaraveltools/alertas`.

---

## Instalación paso a paso

### Paso 1 — Composer

```bash
composer require mylaraveltools/panel
```

**Desarrollo local** (path repository):

```json
{
  "repositories": [
    { "type": "path", "url": "../minimalist-panel-library" }
  ],
  "require": {
    "mylaraveltools/panel": "@dev"
  }
}
```

### Paso 2 — Instalar el panel

```bash
php artisan panel:install
```

Esto publica `config/panel.php`, registra rutas en `/admin` y prepara Livewire.

### Paso 3 — Tailwind

En `tailwind.config.js` incluye las vistas del paquete y activa modo oscuro por clase:

```js
export default {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './vendor/mylaraveltools/panel/resources/views/**/*.blade.php',
  ],
};
```

### Paso 4 — Alpine + Livewire

En `resources/js/app.js`, **no importes Alpine en rutas del panel** (`/admin/*`). Livewire lo gestiona:

```js
const panelPath = '/admin';

if (!window.location.pathname.startsWith(panelPath)) {
  import('alpinejs').then(({ default: Alpine }) => {
    window.Alpine = Alpine;
    Alpine.start();
  });
}
```

> Importar Alpine en `/admin/login` rompe `wire:submit` (el formulario no envía nada).

### Paso 5 — Compilar y probar

```bash
npm run build
php artisan serve
```

Abre `http://127.0.0.1:8000/admin` — verás login/registro si `auth.enabled` es `true`.

**APP_URL** debe coincidir con host y puerto (`http://127.0.0.1:8000`).

---

## Tu primer CRUD en 5 minutos

### 1. Crear el Resource

```bash
php artisan panel:make-resource Product --model=Product
```

### 2. Definir formulario y tabla

```php
// app/Panel/Resources/ProductResource.php
final class ProductResource extends Resource
{
    protected static string $model = Product::class;
    protected static ?string $label = 'Productos';
    protected static ?string $icon = 'package';

    public static function form(): array
    {
        return [
            TextField::make('name')->label('Nombre')->required(),
            NumberField::make('price')->label('Precio')->min(0),
        ];
    }

    public static function table(): array
    {
        return [
            TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('price')->label('Precio')->sortable(),
        ];
    }
}
```

### 3. Listo

Auto-discovery en `app/Panel/Resources/` (configurable). URL: `/admin/resources/products` (slug = kebab del modelo; puedes fijarlo con `protected static ?string $slug = 'productos';`).

---

## Configuración (`config/panel.php`)

Toda la configuración vive en este archivo (compatible con `config:cache`). No hace falta `.env`, aunque puedes usarlo si prefieres.

| Clave | Descripción | Default |
|-------|-------------|---------|
| `path` | Prefijo URL | `admin` |
| `guard` | Guard de auth | `web` |
| `brand.name` | Nombre en sidebar | `Panel` |
| `brand.logo` | URL del logo (`null` = icono) | `null` |
| `per_page` | Registros por página | `15` |
| `forms_in_modal` | Crear/editar en modal | `true` |
| `discovery` | Auto-discovery Resources | `enabled` |
| `pages` | Auto-discovery Pages | `enabled` |
| `permissions` | Spatie/Gate | `disabled` |
| `navigation` | Menú lateral (`null` = auto) | `null` |
| `widgets` | Dashboard | `[]` |
| `import` | Import CSV/Excel | `enabled` + `preview` + `guided_summary` |
| `forms` | Modal: validación inline, borrador, foco | ver [UX del listado](#ux-del-listado) |
| `performance` | Debounces, skeleton, caché filtros, cursor | ver [AGENTS.md](AGENTS.md#guía-ux-del-listado) |
| `layout.index` | Filas clicables, columnas, vista rápida, bulk | ver [UX del listado](#ux-del-listado) |
| `impersonation` | Suplantar usuarios | `disabled` |
| `theme.preset` | Preset visual (`minimal`, `corporate`, `contrast`, `ocean`) | `minimal` |
| `extensions` | Vistas custom de campos/columnas y widgets | `[]` |
| `version` | Texto en sidebar (`null` = paquete) | `null` |

### Tema monocromático

```php
'theme' => [
    'default' => 'dark',
    'font' => 'Plus Jakarta Sans',
    'colors' => [
        'primary' => '#000000',
        'primary_dark' => '#ffffff',
        'accent' => '#525252',
        'success' => '#16a34a',
        'danger' => '#dc2626',
        'warning' => '#ca8a04',
    ],
    'light' => [ /* bg, surface, card, border, heading, text, muted… */ ],
    'dark' => [ /* … */ ],
],
```

Variables CSS: `--panel-primary`, `--panel-bg`, etc. Toggle claro/oscuro en el footer del sidebar (persiste en `localStorage`).

### UX del listado

Listados (`ResourceIndex`) con filtros en URL, carga optimista, selección masiva y atajos de teclado. Guía completa para agentes y desarrolladores: **[AGENTS.md — Guía UX del listado](AGENTS.md#guía-ux-del-listado)**.

Resumen de `config/panel.php`:

| Área | Claves principales | Default |
|------|-------------------|---------|
| Filtros | `layout.filters.mode`, `default_open`, `remember_state` | `collapsible` |
| Tabla | `layout.index.clickable_rows`, `mobile_cards`, `column_toggle`, `quick_view` | `true` |
| Bulk | `layout.index.bulk_preview`, `bulk_select_all_max` | preview on, máx. 500 |
| Rendimiento UX | `performance.skeleton_delay_ms`, `search_debounce_ms` | 50 ms / 200 ms |
| Rendimiento DB | `performance.eager_load_columns`, `filter_options_cache` | `true` |
| Formulario modal | `forms.validate_inline`, `draft_autosave`, `focus_on_open` | `true` |

Atajos: `/` o `Ctrl+F` → buscador; `↑`/`↓` + `Enter` → filas; `Shift+clic` → vista rápida.

Auditoría: `php artisan panel:audit-rendimiento` (N+1 e índices sugeridos).

### Multi-panel (varios paneles en la misma app)

```php
// config/panel.php
'default' => 'admin',
'panels' => [
    'admin' => require __DIR__.'/panel-admin.php',
    'cliente' => require __DIR__.'/panel-cliente.php',
],
```

- Sin `panels` (o vacío): un panel, rutas `panel.*` (comportamiento actual).
- Con 2+ paneles: rutas `panel.admin.dashboard`, `panel.cliente.login`, etc.
- Helper: `panel_route('dashboard')` — usa el panel del request actual.
- Stub ejemplo: `stubs/multi/panel-cliente.stub.php`

### Presets de tema

```php
'theme' => [
    'preset' => 'corporate',  // minimal | corporate | contrast | ocean
    'colors' => [
        'primary' => '#ff0000',  // sobrescribe solo el preset
    ],
],
```

### Extensibilidad (campos, columnas, widgets)

**Por config** (`config/panel.php` → `extensions`):

```php
'extensions' => [
    'field_views' => ['rating' => 'panel-custom.fields.rating'],
    'column_views' => ['rating' => 'panel-custom.columns.rating'],
    'widgets' => [],
],
```

**Por código** (`AppServiceProvider::boot`):

```php
use MyLaravelTools\Panel\Facades\PanelExtensions;

PanelExtensions::registrarVistaCampo('rating', 'panel-custom.fields.rating');
PanelExtensions::registrarWidget(StatWidget::make('Total', fn () => Model::count()));
```

**Campo custom en un Resource:**

```php
CustomField::make('payload')
    ->type('json-editor')
    ->view('panel-custom.fields.json-editor')
    ->label('Datos JSON'),
```

**Campos integrados nuevos:** `ColorField`, `DateTimeField`, `KeyValueField` (pares clave/valor → JSON).

**Actualizar vistas publicadas:**

```bash
php artisan panel:upgrade-views --dry-run
php artisan panel:upgrade-views --force
```

**Actualizar config tras `composer update`:**

```bash
php artisan panel:upgrade-config --dry-run
php artisan panel:upgrade-config
php artisan panel:doctor
```

Fusiona claves nuevas del paquete en `config/panel.php` (crea backup `.bak`). `panel:doctor` avisa si faltan claves respecto a la versión instalada.

### Layout y apariencia

```php
'layout' => [
    'density' => 'compact',           // comfortable | compact
    'content_width' => 'boxed',       // full | boxed
    'sidebar_collapsible' => true,
    'show_breadcrumbs' => true,
    'footer_links' => [
        ['label' => 'Ayuda', 'route' => 'panel.dashboard'],
        ['label' => 'Web', 'url' => 'https://ejemplo.com', 'external' => true],
    ],
],

'brand' => [
    'name' => 'Mi Panel',
    'logo' => '/img/logo.svg',
    'logo_height' => '2.5rem',
    'favicon' => '/favicon.ico',
    'tagline' => 'Gestiona tu negocio',
],

'auth_ui' => [
    'layout' => 'split',              // centered | split
    'image' => '/img/auth-side.jpg',
    'background' => 'linear-gradient(135deg, #0f172a, #1e3a5f)',
    'show_tagline' => true,
],

'customization' => [
    'css' => '.panel-sidebar { border-right-width: 2px; }',
    'head_view' => 'panel-custom.head',
],
```

**RepeaterField** — filas con varias columnas (JSON):

```php
RepeaterField::make('lineas')
    ->columns(['concepto' => 'Concepto', 'importe' => 'Importe'])
    ->minRows(1)
    ->maxRows(10),
```

### Máxima personalización (v0.24)

**Modos de layout** — `sidebar` (por defecto), `topbar` o `dual` (barra superior + lateral):

```php
'layout' => [
    'mode' => 'sidebar',              // sidebar | topbar | dual
    'sidebar_position' => 'left',     // left | right
    'table_striped' => true,
    'table_compact' => false,
    'global_search' => true,
    'per_page_options' => [15, 25, 50, 100],
],
```

**Slots Blade** — inyecta vistas en puntos del layout sin publicar todo el paquete:

```php
'slots' => [
    'sidebar.before' => 'mi-app.panel.slots.aviso',
    'main.after' => 'mi-app.panel.slots.analytics',
    'topbar.end' => 'mi-app.panel.slots.acciones',
],
```

Slots disponibles: `sidebar.before`, `sidebar.after`, `main.before`, `main.after`, `topbar.start`, `topbar.end`, `footer.before`.

También vía código en `AppServiceProvider`:

```php
use MyLaravelTools\Panel\Support\PanelExtensions;

PanelExtensions::registrarSlot('main.before', 'mi-app.panel.banner');
```

**Import upsert** — actualiza registros existentes en lugar de fallar:

```php
'import' => [
    'upsert' => true,
    'upsert_key' => 'email',          // global; el Resource puede sobreescribir
],
```

```php
// En tu Resource
public static function importUpsertKey(): ?string
{
    return 'sku';
}
```

**Hooks en Resources:**

```php
public static function navigationBadge(): ?string
{
    return (string) static::model()::where('is_active', false)->count();
}

public static function hiddenFromNavigation(): bool
{
    return ! auth()->user()?->can('view settings');
}

public static function perPageOptions(): array
{
    return [10, 25, 50];
}
```

**Presets de tema propios** — archivo PHP que devuelve un array de presets:

```php
'theme' => [
    'preset' => 'mi-marca',
    'presets_file' => config_path('panel-theme-presets.php'),
],
```

**Instalación con demo:**

```bash
php artisan panel:install --demo
```

---

## Autenticación y perfil

Auth integrada en `/admin/login` y `/admin/register` (tabla `users` de Laravel):

```php
'auth' => [
    'enabled' => true,
    'register' => true,
    'register_role' => 'viewer',      // Spatie, opcional
    'password_reset' => true,
    'email_verification' => false,    // requiere MustVerifyEmail
],
```

- Auth externa (Breeze/Fortify): `'enabled' => false`, `'login_route' => 'login'`.
- Tras login: **recarga completa** al dashboard (sin loader SPA). Botón «Entrando» con puntos animados solo durante el POST.
- Recuperar contraseña: `/admin/forgot-password`.
- Perfil: `/admin/profile` — `'profile.enabled' => true`.

---

## Permisos (Spatie) y suplantación

### Spatie Laravel Permission

```bash
composer require spatie/laravel-permission
```

```php
'permissions' => [
    'enabled' => true,
    'panel_access' => 'access panel',
    'resources' => true,              // RoleResource + PermissionResource
    'manage_permission' => 'manage users',
],
```

- `RolesField` / `RolesColumn` en usuarios.
- `PermissionsField` / `PermissionsColumn` en roles.
- En Pages: `protected static ?string $permission = 'view reports'`.
- Policies: `php artisan panel:make-policy Product` → extiende `ResourcePolicy` (deny-by-default).

### Suplantación de usuario

Navega el panel **como otro usuario** (permisos, menú y policies reales):

```php
'impersonation' => [
    'enabled' => true,
    'permission' => 'impersonate users',
    'exclude_ids' => [],
    'banner' => true,
],
```

1. En el resource del modelo `User`, menú ⋮ → **Entrar como**.
2. Aparece una tarjeta en el **sidebar** (encima del perfil) con botón **Salir**.
3. Requiere permiso Spatie/Gate. No puedes suplantarte a ti mismo.

---

## Navegación y páginas custom

### Menú con grupos

```php
// config/panel.php
'navigation' => require __DIR__.'/panel-navigation.php',
```

```php
return [
    ['resource' => ProductResource::class],
    ['page' => SettingsPage::class],
    [
        'type' => 'group',
        'label' => 'Catálogo',
        'icon' => 'package',
        'children' => [
            ['resource' => ProductResource::class],
            ['resource' => CategoryResource::class],
        ],
    ],
];
```

- Búsqueda global: **Cmd/Ctrl+K**.
- No uses `route()` al cargar el config; usa la clave `'route' => 'panel.dashboard'`.

### Páginas custom (no CRUD)

```bash
php artisan panel:make-page Settings
```

```php
final class SettingsPage extends Page
{
    protected static ?string $label = 'General';
    protected static ?string $slug = 'settings-general';

    public static function view(): string
    {
        return 'panel.pages.settings-general';
    }
}
```

Ruta: `/admin/pages/{slug}`. Vista con `<x-panel::page-header>`.

---

## Importar y exportar datos

### Export

Botones **CSV**, **XLSX** y **PDF** en listados. Con filas seleccionadas, exporta solo la selección.

### Import (con vista previa)

```php
'import' => [
    'enabled' => true,
    'preview' => true,
    'upsert' => true,
    'upsert_key' => null,
],
```

1. Botón **Importar** en el listado (permiso `create`).
2. Sube `.csv`, `.txt`, `.xlsx`, `.xls`.
3. Revisa filas válidas/errores → confirma.
4. Con `upsert` activo: crea nuevos y actualiza existentes según `importUpsertKey()` del Resource.

Personaliza columnas con `Field::importable(false)` o `Resource::import()`.

---

## Dashboard y widgets

```php
'widgets' => [
    ResourceCountWidget::make(ProductResource::class),
    StatWidget::make('Activos', fn () => Product::where('is_active', true)->count())
        ->icon('check-circle'),
    ChartWidget::make('Ventas', 'bar', fn () => [
        'labels' => ['Ene', 'Feb'],
        'values' => [12, 19],
    ])->themeColors(),
    ViewWidget::make('Custom', 'panel.widgets.mi-vista', fn () => ['total' => 100])
        ->columnSpan(2),
],
```

Tipos ChartWidget: `bar`, `line`, `pie`, `doughnut`, `progression`. Gráficos reactivos al tema y SPA.

---

## Relaciones entre modelos

Desde la vista **Ver** del registro padre:

```php
public static function relations(): array
{
    return [
        RelationManager::make('products', ProductResource::class),
        RelationManager::hasOne('profile', ProfileResource::class),
        RelationManager::belongsToMany('tags', TagResource::class),
        RelationManager::morphMany('reviews', ReviewResource::class),
        RelationManager::morphToMany('tags', TagResource::class),
    ];
}
```

---

## Tema, layout y SPA

- **Sin header global** — cada vista usa `<x-panel::page-header>` (título + breadcrumbs).
- **Sidebar footer:** perfil, idioma, tema, versión, logout.
- **SPA:** `wire:navigate`, loader con porcentaje `0%`–`100%`, sidebar persistente.
- **Livewire:** mantén `navigate.show_progress_bar => true` en `config/livewire.php` (la barra NProgress se oculta vía CSS del panel).

### Fields y Columns

**Fields:** `TextField`, `EmailField`, `PasswordField`, `TextareaField`, `BooleanField`, `SelectField`, `BelongsToField`, `NumberField`, `DateField`, `DateTimeField`, `ColorField`, `KeyValueField`, `CustomField`, `FileField`, `ImageField`, `RichTextField`, `RolesField`, `PermissionsField`.

**Columns:** `TextColumn`, `BooleanColumn`, `DateTimeColumn`, `BadgeColumn`, `ColorColumn`, `BelongsToColumn`, `ImageColumn`, `RolesColumn`, `PermissionsColumn`.

**Filtros:** `SelectFilter`, `BooleanFilter`, `DateRangeFilter`, `MultiSelectFilter`.

**Formularios:** `Section::make()`, `Tab::make()`, soft deletes, bulk actions, RowAction.

---

## Comandos Artisan

| Comando | Descripción |
|---------|-------------|
| `php artisan panel:install` | Instalar panel |
| `php artisan panel:install --demo` | Instalar + navigation stub y PostResource ejemplo |
| `php artisan panel:install --starter` | Kit completo: demo + modelo Post + migración + widget dashboard |
| `php artisan panel:install --saas` | Kit SaaS: tenant + extensiones + vistas + widget |
| `php artisan panel:install --multi` | Multi-panel: `panel-admin.php`, `panel-cliente.php` y raíz `panels` |
| `php artisan panel:scaffold Name --policy --widget=resource-count` | Resource + policy + widget en un paso |
| `php artisan panel:make-resource Name` | Crear Resource |
| `php artisan panel:make-page Name` | Crear página custom |
| `php artisan panel:make-policy Name` | Crear Policy |
| `php artisan panel:make-widget Name --type=chart` | Crear clase widget para el dashboard |
| `php artisan panel:doctor` | Diagnosticar instalación del panel |
| `php artisan panel:audit-rendimiento` | Auditar N+1 e índices sugeridos en resources |
| `php artisan panel:upgrade-config` | Fusionar config con claves nuevas del paquete |
| `php artisan panel:upgrade-views` | Actualizar vistas publicadas |
| `php artisan vendor:publish --tag=panel-config` | Publicar config |
| `php artisan vendor:publish --tag=panel-views` | Publicar vistas Blade |
| `php artisan vendor:publish --tag=panel-documentation` | Copiar `documentation/panel/` al proyecto |

### Documentación interactiva (playground)

Ruta pública **`/playground`** (sin login) — `documentation.enabled` y `documentation.path`:

- Panel **FAKE** a pantalla completa + controles laterales
- Catálogo de todas las opciones de `config/panel.php`
- Vista previa en vivo (layout, marca, tema…)
- Markdown: `documentation/panel/README.md`

---

## Personalizar vistas

Si publicas vistas en `resources/views/vendor/panel/`, **sobreescriben** las del paquete.

Tras actualizar el paquete:

```bash
php artisan vendor:publish --tag=panel-views --force
php artisan view:clear
```

Si no publicas vistas, Laravel usa las del vendor directamente (recomendado hasta que edites Blade).

---

## Solución de problemas

| Problema | Solución |
|----------|----------|
| Login no envía el formulario | No importes Alpine en rutas `/admin/*` |
| `Alpine is not defined` | `show_progress_bar => true` en `config/livewire.php` |
| Estilos rotos | Incluye vistas del paquete en `tailwind.config.js` y `npm run build` |
| Feature nueva no aparece | Republica vistas con `--force` o borra `resources/views/vendor/panel/` |
| 404 en `/admin/resources/users` | El slug por defecto es `user` (singular); define `$slug = 'users'` |
| «Entrar como» no visible | Permiso `impersonate users` + `php artisan db:seed` con ese permiso |
| Redirecciones raras en login | `APP_URL` con host y puerto correctos |

---

## Proyecto demo

Carpeta `panel-demo/` con catálogo, ventas, Spatie, gráficos, import y suplantación.

**Demo online:** despliega con [panel-demo/DEPLOY.md](../panel-demo/DEPLOY.md) y `render.yaml` (Render.com). Playground público en `/playground`.

```bash
cd panel-demo
composer install && npm install && npm run build
php artisan migrate:fresh --seed
php artisan serve
```

| Usuario | Email | Password |
|---------|-------|----------|
| Admin | `admin@panel.test` | `password` |
| Editor | `editor@panel.test` | `password` |

Ver `panel-demo/README.md` para rutas y features de prueba.

---

## Desarrollo y tests

```bash
cd minimalist-panel-library
composer test
```

- Contexto para agentes/IA: [AGENTS.md](AGENTS.md)
- Publicar en Packagist: [PUBLISHING.md](PUBLISHING.md) — `composer release:check` antes de etiquetar
- Historial: [CHANGELOG.md](CHANGELOG.md)

---

## Roadmap

- [x] CRUD, SPA, Excel/PDF, búsqueda global, i18n, tests, CI
- [x] RowAction, modales, skeletons, filtros avanzados
- [x] Forms en modal, tabs, export PDF
- [x] Policies, páginas custom, permisos Spatie
- [x] Auth integrada, reset password, perfil
- [x] Import con preview, ChartWidget, ViewWidget, email verify
- [x] Auth UX (v0.20), suplantación de usuario (v0.21)
- [x] Packagist — `mylaraveltools/panel`
- [x] Extensibilidad — presets tema, PanelExtensions, campos custom (v0.22)
- [x] Layout — densidad, boxed, sidebar colapsable, auth split, RepeaterField (v0.23)
- [x] Máxima personalización — topbar/dual, slots, upsert, tablas, presets propios (v0.24)
- [x] Playground público, gráficos interactivos, `panel:doctor`, `panel:make-widget` (v0.25)
- [x] Layout móvil pulido — `mobile-bar`, drawer, modos sidebar/topbar/dual (v0.26)
- [x] Starter kit — `panel:install --starter`, `panel:scaffold` (v0.27)
- [x] Playground ampliado — auth interactivo, ViewWidget, slots visuales (v0.27)
- [x] Multi-panel — `panels` + `panel_route()` + contexto por request (v0.28)
- [x] Instalador multi — `panel:install --multi` + doctor multi-panel + slots playground (v0.29)
- [x] Playground import/permisos + shell layout topbar/dual (v0.30)
- [x] Guía extensiones + smoke CI panel-demo (v0.31)
- [x] Kit SaaS — `panel:install --saas` (v0.32)
- [x] Upgrade config + smoke ampliado — `panel:upgrade-config` (v0.33)
- [x] Doctor config + install tests + playground panel-demo (v0.34)
- [x] panel-demo auth unificada + smoke import/suplantación (v0.35)
- [x] Playground RelationManager + multi-panel (v0.36)
- [x] Publicación Packagist + demo online (PUBLISHING, DEPLOY, render.yaml) (v0.37.0)
- [x] Loader SPA — watchdog, Escape y `panelSpaLoader.ocultar()` (v0.38.0)
- [x] Páginas de error con tema — 403, 404, 419, 422, 429, 500, 503; diseño alineado con el panel e iconos `x-panel::icon` (v0.40.1)
- [x] UX listados — estado vacío contextual, limpiar búsqueda, cabecera sticky, focus-visible, Escape en confirmación (v0.41.0)
- [x] Formularios modernos — bordes redondeados, pestañas pill, secciones y file upload (v0.42.0)
- [x] Filtros colapsables en listados — panel plegable, badge activos, grid ordenado (v0.43.0)
- [x] UX listados v2 — chips, contador, export compacto, atajos `/` y `Ctrl+F` (v0.46.0)
- [x] Tabla sticky, filas clicables, tarjetas móvil, filtros inteligentes (v0.47.0)
- [x] Rendimiento percibido — `PanelRendimiento`, skeleton 50 ms, loader SPA 120 ms (v0.48.0)
- [x] Capa 1 listados — barra bulk fija, copiar enlace, teclado ↑↓+Enter, RelationPanel alineado (v0.49.0)
- [x] Capa 2 listados — columnas ocultables, vista rápida, presets filtros, selección global (v0.50.0)
- [x] Capa 3 listados — formulario inline/borrador, import guiado, preview bulk (v0.51.0)
- [x] Capa 4 rendimiento — `PanelConsultas`, caché opciones, cursor opcional, `panel:audit-rendimiento` (v0.52.0)
- [x] Capa 5 DX — roadmap actualizado, guía UX del listado en AGENTS (v0.53.0)

---

## Licencia

MIT — [Alberto Gallardo Morales](mailto:gallardev.98@gmail.com)
