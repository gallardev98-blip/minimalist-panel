# AGENTS — Minimalist (`mylaraveltools/minimalist`)

## Estado actual

**Paquete Composer:** `mylaraveltools/minimalist` — `composer require mylaraveltools/minimalist` (namespace PHP: `MyLaravelTools\Panel`).

**Fase 9** (2026-06-18): páginas custom (`Page`, `panel:make-page`, ruta `pages/{slug}`) e integración opcional de permisos Spatie/Gate (`PanelPermission`, filtrado de navegación).

**Fase 10** (2026-06-19): autenticación integrada — login/registro/logout en `{panel.path}/login` con layout guest y modelo `users` de Laravel.

**Fase 11** (2026-06-19): recuperar contraseña (`ForgotPassword`, `ResetPassword`) y `RolesField`/`RolesColumn` para Spatie.

**Fase 12** (2026-06-19): perfil de usuario — `/admin/profile` (nombre, email, contraseña); enlace en sidebar.

**v0.13.0** (2026-06-19): layout sin header — `<x-panel::page-header>` (título + breadcrumbs en la misma fila); footer del sidebar con tema / versión / logout; loader SPA con `%`.

**v0.14.0** (2026-06-19): resources Spatie integrados — `RoleResource`, `PermissionResource`, `PermissionsField`/`PermissionsColumn`; auto-registro cuando `permissions.enabled` + Spatie instalado.

**v0.16.0** (2026-06-20): namespace PHP `MyLaravelTools\Panel` (antes `Panel\Minimalist`); alineado con `MyLaravelTools\Alertas`.

**Config-first** (2026-06-19): `config/panel.php` sin `env('PANEL_*')` por defecto.

**Fase 8** (2026-06-18): Policies de Laravel (`ResourcePolicy`, `panel:make-policy`, auto-registro).

**v0.7.1** (2026-06-18): pulido UI interiores (show, form, relation-panel, dashboard), toolbar exports, `PUBLISHING.md`.

**Fase 6** (2026-06-18): RowAction, confirm modal, skeletons, filtros avanzados, breadcrumbs con título, tests Livewire.

**Menú lateral con grupos** (2026-06-18): `NavigationBuilder` soporta `type: group` con hijos desplegables (Alpine), badges, enlaces a Resources o rutas nombradas. Ver `config/panel-navigation.php` en `panel-demo`.

## Navegación con grupos

- `NavigationBuilder::build()` — normaliza ítems `link` y `group`
- `NavigationBuilder::flatten()` — aplanado para búsqueda global
- `NavigationBuilder::linkIsCurrent()` — estado activo en sidebar
- Partial `partials/nav-links.blade.php` — triggers desplegables + sublinks
- Config: clave `route` (no `route()` en tiempo de carga del config)

## Fase 6 — Novedades

### RowAction

```php
use MyLaravelTools\Panel\Actions\RowAction;

public static function rowActions(): array
{
    return [
        RowAction::view(),
        RowAction::edit(),
        RowAction::make('duplicate')
            ->label('Duplicar')
            ->icon('copy')
            ->handle(fn (Model $record) => /* ... */),
    ];
}
```

Por defecto: `view`, `edit`, `delete` (+ `restore`, `forceDelete` con soft deletes).

### Modal de confirmación

- Trait `ConfirmsPanelActions` en `ResourceIndex`
- Partial `partials/confirm-modal.blade.php`
- Sustituye `wire:confirm` del navegador en bulk y row actions

### Skeleton loaders

- `partials/skeleton-table.blade.php` + clase `.panel-skeleton`
- Visible durante búsqueda, filtros, ordenación y paginación

### Filtros y fields nuevos

| Clase | Uso |
|-------|-----|
| `DateRangeFilter` | Rango `from` / `to` en columna fecha |
| `MultiSelectFilter` | `whereIn` con select multiple |
| `MultiSelectField` | Campo formulario multi-valor |

### Breadcrumbs con título

```php
protected static ?string $recordTitleAttribute = 'name';
// o automático desde primera columna searchable
Resource::recordTitle($record);
```

### Tests (27)

- `tests/Feature/ResourceIndexTest.php` — Livewire CRUD flow + form modal create/edit
- `tests/Unit/RowActionTest.php`
- `tests/Unit/FormSchemaTest.php` — tabs y sections
- `tests/Unit/PdfExporterTest.php`
- `tests/Unit/ResourceAuthorizerTest.php` — policies + hooks

## Fase 7 — Novedades

### Formularios en modal

- Config: `panel.forms_in_modal` (default `true` en `config/panel.php`)
- Trait `ManagesResourceFormModal` en `ResourceIndex` (+ `WithFileUploads`)
- Partial `partials/form-modal.blade.php`
- Crear: `wire:click="openCreateFormModal"` | Editar: `openEditFormModal($id)` en row actions
- El registro se resuelve por `formRecordId` en cada request (propiedades privadas no persisten en Livewire)

### Tabs en formularios

```php
use MyLaravelTools\Panel\Forms\Section;
use MyLaravelTools\Panel\Forms\Tab;

Tab::make('General', [
    Section::make('Datos', [ TextField::make('name') ]),
]),
```

- `FormSchema::hasTabs()`, `FormSchema::tabs()`, flatten recursivo en `fields()`
- Partials: `form-tabs.blade.php`, `form-schema-items.blade.php`
- Estilos: `.panel-form-tabs`, `.panel-form-tab--active` en `theme-styles.blade.php`

### Export PDF

- `PdfExporter` — vista `exports/resource-pdf.blade.php`, papel A4 landscape
- Botones compactos CSV / Excel / PDF en toolbar única (exportan selección si hay filas marcadas)
- Dependencia: `dompdf/dompdf` ^3.0

### Toolbar unificada (listado)

- Una sola barra: selección + bulk (eliminar/restaurar) + crear + exports
- Filtros con `.panel-filter-field` (label separado del input)
- Buscador `.panel-search` (icono sin solapar placeholder)
- Acciones de fila: menú desplegable con icono engranaje, columna alineada al header

### Layout shell

- `.panel-shell` — CSS Grid en desktop (`sidebar | main`, sin header global)
- Breadcrumbs en `<x-panel::page-header>` — misma fila que el título (título izq., miga de pan der.)
- Sidebar footer: perfil arriba; fila inferior con tema (izq.), versión (centro) y logout (der.)
- Sidebar `fixed` en móvil, `relative` en grid desktop
- SPA loader (`partials/spa-loader.blade.php` + `spa-navigation.blade.php`): porcentaje entero en el anillo (`0%`→`100%`); progreso simulado (Livewire no expone % real de fetch); si `event.detail.cached`, salta a `100%`
- **Loader en auth:** layout guest pasa `fullscreen => true` → clase `panel-spa-loader--fullscreen` (pantalla completa, sin offset de sidebar); al iniciar navegación desde login se bloquea fullscreen hasta ocultar el loader
- Livewire: mantener `navigate.show_progress_bar = true` en `config/livewire.php` — si es `false`, Livewire añade `data-no-progress-bar` y lanza `Alpine is not defined` al cargar; la barra NProgress se oculta vía CSS (`#nprogress` en theme-styles)
- **`panelApp()` en `<head>`** del layout app — definir antes de `@livewireScripts` para que Alpine resuelva `x-data` en `<body>`
- **BOM UTF-8** — las vistas no deben guardarse con BOM (PowerShell `Set-Content` lo añade); un BOM dentro de `.panel-shell` rompe el CSS Grid y crea hueco superior

## Fase 10 — Autenticación integrada (v0.10.0)

- Rutas: `panel.login`, `panel.register`, `panel.logout`
- Livewire: `MyLaravelTools\Panel\Livewire\Auth\Login`, `Register`
- Layout: `panel::layouts.guest` (mismo tema monocromático)
- Tras login/registro: **navegación SPA** (`navigate: true`) con el mismo loader del panel; el layout guest incluye `spa-loader` + `spa-navigation`
- **`spa-navigation` en auth:** `cleanupLayoutArtifacts()` solo corre si existe `.panel-shell` (transición post-login); no mostrar loader ni limpiar DOM al navegar **hacia** rutas auth (`/login`, `/register`, etc.) — de lo contrario el login parpadea y desaparece
- Enlace de marca del guest layout **sin** `wire:navigate` (misma ruta login; evita navigate innecesario)
- **Alpine en auth:** no importar `alpinejs` en `app.js` para rutas `/admin/*` — Livewire lo arranca; importarlo rompe `wire:submit` en login
- **Guest layout sin Alpine en `<body>`** — el toggle de tema usa JS vanilla en `<head>`; Livewire gestiona el formulario de login (`wire:submit.prevent`)
- **Auth + Alertas (`mylaraveltools/alertas`)** — credenciales incorrectas → toast vía `DispatchesPanelAuthAlert` + evento Livewire `alerta`; sin Alertas instalado, fallback a `@error` en el campo email (sin bloque resumen duplicado). Layout guest/app incluyen `partials/integrations/alertas.blade.php` si el paquete está presente (`panel.integrations.alertas`)
- **Locale Livewire** — `PanelLocale::apply()` en middleware HTTP y en hook `component.booted` de componentes `MyLaravelTools\Panel\*`; mensajes de validación auth en `panel::panel.validation.*`
- **Tema auth tras morph** — `panelAuthApplyTheme()` reaplica `dark` desde `localStorage` en `Livewire.hook('morph.updated')`; toggle con `onclick` + `@persist` (evita doble listener y parpadeo de tema al validar)
- **`PanelAuth::redirectTargetAfterAuth()`** — redirección tras login/registro; ignora `url.intended` si apunta al login
- **APP_URL** debe incluir host y puerto correctos (`http://127.0.0.1:8000`); redirecciones auth usan URLs relativas (`absolute: false`)
- Estilos auth solo en `body.panel-auth-body` — nunca `overflow: hidden` en `html` (rompe el grid al navegar)
- **BOM UTF-8** en `theme-styles.blade.php` rompe el layout del admin; guardar siempre UTF-8 sin BOM
- Config: `panel.auth.enabled`, `register`, `register_role` (Spatie)
- Desactivar con `'enabled' => false` en `config/panel.php` para usar Breeze/Fortify

## Fase 9 — Páginas custom y permisos (v0.9.0)

### Page API

```bash
php artisan panel:make-page Settings
```

- `MyLaravelTools\Panel\Pages\Page` — slug, label, icon, `view()`, `data()`, `canAccess()`
- Auto-discovery en `app/Panel/Pages`
- Ruta: `{panel.path}/pages/{slug}` → Livewire `PanelPage`
- Navegación: `['page' => SettingsPage::class]` en `config/panel-navigation.php`

### Permisos (Spatie opcional)

```php
// config/panel.php
'permissions' => [
    'enabled' => true,
    'panel_access' => 'access panel',
    'resources' => true,
    'manage_permission' => 'manage users',
],
```

- `MyLaravelTools\Panel\Support\PanelPermission` — `check()`, `panelAccessGranted()`, `manageAccessPermission()`
- `SpatieResourceRegistrar` — `RoleResource` + `PermissionResource` cuando `enabled` + Spatie + `resources => true`
- Slugs: `roles`, `permissions` — el host puede sobreescribir con su propio Resource del mismo slug
- `PermissionsField` / `PermissionsColumn` — `syncPermissions()` en roles (como `RolesField` en usuarios)
- `EnsurePanelAccess` — exige `panel.permissions.panel_access` si `enabled`
- Pages: `protected static ?string $permission = 'view reports'`
- Nav: clave `permission` en enlaces manuales; filtra resources (`viewAny`) y pages (`canAccess`)

### Demo (`panel-demo`)

- `spatie/laravel-permission` + `PanelPermissionSeeder` (roles: admin, editor, viewer)
- Páginas: `SettingsPage`, `SalesReportPage`, `LowStockReportPage`, `CustomerActivityPage`, `OnlineStoreSettingsPage`
- Usuarios: `admin@panel.test` / `editor@panel.test` (password: `password`)

### Publicación

- Repo Git inicializado en `minimalist-panel-library/` con tags `v0.9.0` y `v0.10.0`
- Ver `PUBLISHING.md` para push a GitHub y registro en Packagist

## Fase 8 — Policies (v0.8.0)

### ResourcePolicy + auto-registro

```bash
php artisan panel:make-policy Product
```

- `MyLaravelTools\Panel\Policies\ResourcePolicy` — deny-by-default
- `PolicyRegistrar` en boot si `panel.policies.auto_register`
- `Resource::$policy` o convención `App\Policies\{Model}Policy`
- Autorización: hooks `can*()` **AND** Policy (si existe)

### Demo

- `UserPolicy` — solo `admin@panel.test` (`User::isPanelAdmin()`)
- `ProductPolicy` — cualquier usuario autenticado

### CHANGELOG

Ver `CHANGELOG.md` — versionado semántico desde v0.6.0.

## Fase 7 (v0.7.0)

- Crear/editar en modal (`forms_in_modal`)
- Tabs en formularios (`Tab::make`)
- Export PDF (listado y selección bulk)
- Publicación Packagist — `mylaraveltools/minimalist` en Packagist (ver `PUBLISHING.md`)

## Fases anteriores

- **Fase 6**: RowAction, confirm modal, skeletons, DateRange/MultiSelect
- **Fase 5**: Excel, búsqueda global, Date/File/RichText, i18n, CI
- **Fase 4**: sections, BelongsToMany, breadcrumbs
- **Fase 3**: RelationManager, widgets, CSV, SPA
