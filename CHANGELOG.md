# Changelog

All notable changes to `mylaraveltools/minimalist` are documented in this file.

## [Unreleased]

### Changed

- **Rebranding:** paquete Composer `mylaraveltools/minimalist` (antes `gallardev/minimalist`)
- **Breaking:** namespace PHP `Panel\Minimalist` → `MyLaravelTools\Panel` (alineado con `MyLaravelTools\Alertas`)

### Fixed

- Login/auth — transición SPA tras login/registro (`navigate: true` + loader en layout guest); Livewire `Auth\Login`; guest sin Alpine en body
- **`/admin/login` parpadeo / formulario invisible** — `spa-navigation` ya no elimina `.panel-auth-shell` al llegar a rutas auth; loader SPA omitido en destinos auth; brand link sin `wire:navigate`

## [0.14.0] - 2026-06-19

### Added

- **`RoleResource`** y **`PermissionResource`** integrados (Spatie) — `/admin/resources/roles` y `/admin/resources/permissions`
- **`PermissionsField`** / **`PermissionsColumn`** — asignación de permisos en roles (`syncPermissions`)
- **`SpatiePermissions`**, **`SpatieResourceRegistrar`**
- Config `panel.permissions.resources` (default `true`) y `panel.permissions.manage_permission` (default `manage users`)
- Tests: `PermissionsFieldTest`, `SpatieResourceRegistrarTest`, `ResourceRegistrySpatieTest`

### Changed

- `ResourceRegistry` — registra built-ins solo si el host no define el mismo slug
- **panel-demo** — elimina resources duplicados; usa los integrados de la librería

## [0.13.0] - 2026-06-19

### Added

- **`SpaLoaderTest`** — comprueba markup del loader con `%`, script de progreso y `page-header` en el layout

### Changed

- **Layout sin barra superior** — se elimina el header global; cada vista usa `<x-panel::page-header>` (título + miga de pan)
- Cabecera de página unificada — título (izq.) y miga de pan (der.) en la misma fila
- **Sidebar footer** — tema (izq.), versión (centro) y cerrar sesión (der.); el perfil queda encima de esa fila
- Grid del shell: `sidebar | main` (sin fila de header)
- SPA loader — porcentaje entero (`0%`–`100%`) en el anillo; cubre desde `top: 0`
- **`panel:install`** — publica `config/livewire.php` si falta y desactiva `navigate.show_progress_bar`
- Config `panel.version` (null = versión del paquete con prefijo `v`)
- **README** y **AGENTS** — layout, SPA, breadcrumbs, roadmap fases 9–13
- **panel-demo** — páginas custom migradas a `<x-panel::page-header>`; `show_progress_bar => false`

### Removed

- `partials/header.blade.php`

## [0.12.1] - 2026-06-19

### Fixed

- Modales crear/editar y confirmación usan clases CSS propias (`.panel-modal-*`) — no dependen solo de Tailwind
- Menú lateral: alineación explícita de grupos desplegables y subenlaces
- SPA loader: bloquea scroll (`panel-scroll-lock`) y resetea posición al navegar

## [0.12.0] - 2026-06-19

### Added

- **Perfil de usuario** — `/admin/profile` para editar nombre, email y contraseña
- Enlace al perfil en sidebar (avatar + nombre)
- Config `panel.profile.enabled` (default `true`)
- `PanelAuth::user()` y `PanelAuth::profileEnabled()`

## [0.11.2] - 2026-06-19

### Fixed

- Inputs auth (login/reset): icono invisible con autofill/focus en modo oscuro — override `-webkit-autofill` y color de icono `--panel-text`

## [0.11.1] - 2026-06-19

### Fixed

- Mensajes de recuperar contraseña en español (`panel.auth.passwords.*`) con `panel.locale` (default `es`)

## [0.11.0] - 2026-06-19

### Added

- **Recuperar contraseña** — `/admin/forgot-password` y `/reset-password/{token}` con layout guest
- **`RolesField`** — asignación de roles Spatie en formularios (`syncRoles` tras guardar)
- **`RolesColumn`** — badges de roles en listados
- **`Field::afterSave()`** + `FieldPayload::persistAfterSave()` para campos relacionales
- Config `panel.auth.password_reset` (default `true`)

### Changed

- Login incluye enlace «¿Olvidaste tu contraseña?» cuando reset está activo

## [0.10.2] - 2026-06-19

### Changed

- **Config-first:** `config/panel.php` usa valores directos en lugar de `env('PANEL_*')`. Edita el archivo tras `panel:install`; compatible con `config:cache`. Opcional: envolver valores con `env()` en el config publicado del host.

## [0.10.1] - 2026-06-19

### Changed

- **Packagist:** nombre del paquete `panel/minimalist` → `gallardev/minimalist` (el vendor `panel` ya estaba reclamado en Packagist)
- Namespace PHP sin cambios: `MyLaravelTools\Panel`


### Added

- **Autenticación integrada** — login, registro y logout en `{panel.path}/login` y `/register`
- **`PanelAuth`** — resolución de modelo User, rutas y redirecciones
- **Livewire** `Auth\Login` y `Auth\Register` con layout guest del panel
- **`RedirectIfPanelAuthenticated`** — invitados autenticados redirigen al dashboard
- **Config** `panel.auth` — `enabled`, `register`, `register_role`, redirecciones
- **Traducciones** `panel.auth.*` (es/en)
- **`AuthTest`** — feature tests de login, registro y logout

### Changed

- `login_route` / `logout_route` por defecto apuntan a `panel.login` / `panel.logout`
- `EnsurePanelAccess` usa `PanelAuth::loginRouteName()`

## [0.9.0] - 2026-06-18

### Added

- **`Page`** — páginas custom (informes, ajustes) fuera del patrón Resource
- **`PageRegistry`** + **`PageDiscovery`** — auto-discovery en `app/Panel/Pages`
- **`PanelPage`** Livewire — renderiza vista Blade con `data()` y layout del panel
- **`panel:make-page`** — genera Page + vista stub
- **`PanelPermission`** — integración opcional Spatie Laravel Permission o Gate
- **Config** `panel.pages`, `panel.permissions`
- **`EnsurePanelAccess`** — comprueba permiso `panel_access` cuando permisos están activos
- **Navegación** — soporte `page` en ítems; filtrado por permiso y `canAccess`/`viewAny`
- **Breadcrumbs** — soporte `panel.pages.show`

### Changed

- **Breaking:** namespace PHP `Alberto\Panel` renombrado a `MyLaravelTools\Panel` (alineado con el paquete `gallardev/minimalist`)

## [0.8.0] - 2026-06-18

### Added

- **`ResourcePolicy`** — base policy con deny-by-default para abilities del panel
- **`PolicyRegistrar`** — auto-registro `Gate::policy()` por Resource al boot
- **`PolicyResolver`** — detecta policy explícita (`$policy`) o convención `{Model}Policy`
- **`panel:make-policy`** — genera policy y enlaza el Resource si existe
- **Config** `panel.policies` — `auto_register`, `namespace`
- **`ResourceAuthorizerTest`** — tests de autorización con Gate
- **Demo** — `UserPolicy` (solo admin), `ProductPolicy` (usuarios autenticados)

### Changed

- `ResourceAuthorizer` — si hay policy registrada, usuario guest siempre denegado
- `UserResource` demo migrado de hooks `can*()` inline a `UserPolicy`

## [0.7.1] - 2026-06-18

### Added

- **PUBLISHING.md** — guía para publicar en Packagist y etiquetar releases

### Changed

- **Toolbar unificada** en listados: exports compactos (CSV / XLSX / PDF), una sola barra sin duplicar bulk exports
- **Buscador** — icono sin solapar placeholder (`.panel-search`)
- **Filtros** — labels con espaciado (`.panel-filter-field`)
- **Acciones de fila** — menú desplegable con engranaje alineado a la cabecera
- **resource-show** — título del registro, tarjeta de detalle pulida
- **resource-form** — footer de acciones y estado de carga al guardar
- **relation-panel** — mismo menú de acciones, modal de confirmación, fix persistencia al editar
- **dashboard** — espaciado y secciones más claras

## [0.7.0] - 2026-06-18

### Added

- **Form modal** — create and edit records in a modal overlay on the index page (`config('panel.forms_in_modal')`, default `true`)
- **Form tabs** — `Tab::make()` to organize form sections into tabbed panels (Alpine.js)
- **PDF export** — `PdfExporter` with list and bulk selection export (`dompdf/dompdf`)
- **Tests** — form modal Livewire tests, `FormSchema` tabs, `PdfExporterTest`

### Changed

- Create button and edit row action open the modal when `forms_in_modal` is enabled
- `ProductResource` demo uses tabs for General / Precio y publicación

## [0.6.0] - 2026-06-18

### Added

- **RowAction** — configurable per-row actions (`view`, `edit`, `delete`, `restore`, `forceDelete`, custom)
- **Confirm modal** — native panel confirmation dialog (replaces browser `wire:confirm`)
- **Skeleton loaders** — table loading state on search, filters, sort and pagination
- **DateRangeFilter** — filter records by date range (`from` / `to`)
- **MultiSelectFilter** — filter by multiple values (`whereIn`)
- **MultiSelectField** — multi-value form field
- **`Resource::recordTitle()`** — human-readable breadcrumb labels on show pages
- **Livewire feature tests** — `ResourceIndexTest` (search, delete, confirm flow)
- **RowActionTest** — unit tests for row actions and record titles

### Changed

- Row actions in table are now driven by `Resource::rowActions()` instead of hardcoded Blade
- Bulk actions use the panel confirm modal
- Breadcrumbs on show pages display the record title instead of `#id`

## [0.5.0] - 2026-06-18

### Added

- Export Excel (`ExcelExporter`, PhpSpreadsheet)
- Global search command palette (Cmd/Ctrl+K)
- Fields: `DateField`, `FileField`, `RichTextField`
- Complete i18n (ES/EN) across views and PHP
- Tests: Breadcrumbs, ResourceQuery, GlobalSearch
- GitHub Actions CI (PHP 8.2–8.4)

## [0.4.0] - 2026-06-18

### Added

- Form sections (`Section::make()`)
- BelongsToMany relation manager
- Breadcrumbs in header
- i18n foundation (`lang/es`, `lang/en`)
- PHPUnit test suite

## [0.3.0]

- RelationManager (HasMany), widgets, CSV export
- Monochrome theme, SPA navigation, Lucide icons

## [0.1.0]

- Declarative CRUD resources, filters, bulk actions, soft deletes
