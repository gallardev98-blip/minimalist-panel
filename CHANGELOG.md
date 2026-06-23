# Changelog

All notable changes to `mylaraveltools/panel` are documented in this file.

## [Unreleased]

## [0.37.0] - 2026-06-23

### Added

- `scripts/verificar-release.php` + `composer release:check`
- CI `.github/workflows/release.yml` en tags `v*`
- `panel-demo/DEPLOY.md` + `render.yaml` (Render.com)
- Badges Packagist en README

### Changed

- `PUBLISHING.md` actualizado a v0.36+ (checklist, upgrade-config, demo online)

## [0.36.0] - 2026-06-23

### Added

- Playground Avanzado: guía **RelationManager** (`PanelRelacionesGuia`) con preview de pestañas
- Playground Avanzado: guía **multi-panel** (`PanelMultiPanelGuia`) con diagrama y código copiable
- Sección técnica `multi_panel` en `PanelDocumentacion`
- Tests `PanelRelacionesGuiaTest`, `PanelMultiPanelGuiaTest`, playground relations/multi

## [0.35.0] - 2026-06-23

### Fixed

- `ImpersonationController` / `LogoutController` — `Redirect::` en lugar de `redirect()` (conflicto con Livewire Redirector)

### Changed

- **panel-demo**: auth unificada (Breeze → `/admin/*`); smoke import CSV y suplantación

## [0.34.0] - 2026-06-23

### Added

- **`panel:doctor`** — avisa si faltan claves en config (`panel:upgrade-config --dry-run`)
- Tests `InstallPanelStarterTest`, `InstallPanelMultiTest`
- `panel-demo`: playground en `/playground` + smoke test
- CI smoke `panel-demo` en PHP 8.2, 8.3 y 8.4

### Changed

- README: sección actualizar config tras `composer update`

## [0.33.0] - 2026-06-23

### Added

- **`panel:upgrade-config`** — fusiona `config/panel.php` con claves nuevas del paquete (`--dry-run`, backup `.bak`)
- **`PanelConfigUpgrader`** — fusión recursiva y listado de claves añadidas
- **`panel:doctor`** — comprueba que `panel_route()` esté cargada y operativa
- Smoke `panel-demo` ampliado: import, página custom, permisos editor/admin, `upgrade-config`
- Tests: `PanelConfigUpgraderTest`, `UpgradeConfigCommandTest`, `NavigationBuilderTest`

## [0.32.0] - 2026-06-23

### Added

- **`panel:install --saas`** — Tenant, TenantResource, vistas `panel/saas/*`, extensiones `saas-plan`, slot y widget
- **`PanelSaasGuia`** — guía CLI en playground Avanzado → Extensiones
- Stubs en `stubs/saas/`
- Test `InstallPanelSaasTest`

### Fixed

- **`panel_route()`** — definición correcta en `helpers.php` y prefijo `\` en llamadas desde namespaces PHP

## [0.31.0] - 2026-06-23

### Added

- **`PanelExtensionesGuia`** — receta end-to-end campo → columna → widget (tipo «rating») en playground Avanzado
- **Smoke tests `panel-demo`** — `PanelSmokeTest` (login, dashboard, productos, `panel:doctor`)
- **CI** — job `smoke-panel-demo` en `.github/workflows/panel-tests.yml`

## [0.30.0] - 2026-06-23

### Added

- **Playground layout shell** — diagrama Sidebar / Topbar / Main en el escenario según `layout.mode`
- **Preview import** — tabla de validación interactiva en Avanzado → Importación
- **Preview permisos** — menú simulado con ítems bloqueados en Avanzado → Permisos
- Zonas `import` y `permisos` en resaltado del playground

### Changed

- `PlaygroundDemo` — datos demo para import y permisos
- `seleccionarSeccionTecnica()` resalta la zona al abrir import o permissions

## [0.29.0] - 2026-06-23

### Added

- **`panel:install --multi`** — genera `panel-admin.php`, `panel-cliente.php` y raíz `panels` en `config/panel.php`
- **`panel:doctor`** — comprueba multi-panel (paths únicos, recuento de paneles)
- **Playground slots** — marcadores `sidebar.after`, `main.before`, `topbar.end` en el escenario demo
- Stub `stubs/multi/panel-admin.stub.php`

### Changed

- `panel:install --multi` migra la config actual a `panel-admin.php` si aún no existe

## [0.28.0] - 2026-06-23

### Added

- **Multi-panel** — varios paneles en la misma app vía `config/panel.php` → `panels` + `default`
- **`PanelManager`** — contexto por panel (config, resources, widgets, slots)
- **`panel_route()`** — helper de rutas consciente del panel actual (`panel.*` o `panel.{id}.*`)
- Middleware **`SetCurrentPanel`** — aplica config del panel en cada request
- Stub ejemplo `stubs/multi/panel-cliente.stub.php`

### Changed

- Rutas con 2+ paneles: `panel.admin.dashboard`, `panel.cliente.login`, etc.
- Un solo panel (sin `panels` o vacío): rutas legacy `panel.*` sin cambios
- `ResourceRegistry` y `PageRegistry` con caché por panel

## [0.27.0] - 2026-06-23

### Added

- **`panel:scaffold`** — resource + policy opcional + widget opcional en un comando (`--policy`, `--widget=stat|chart|resource-count`)
- **`panel:install --starter`** — kit completo: navigation, PostResource, modelo Post, migración, PostCountWidget y parche automático de `config/panel.php`
- **Playground Auth** — pestaña interactiva con vista previa del login (centered/split, fondo, imagen)
- **Playground ViewWidget** — demo en vivo con timeline Blade en el escenario
- **Playground slots** — marcadores visuales `sidebar.before` y `main.after`
- Badge de **modo layout** (sidebar/topbar/dual) en la vista previa del dashboard

### Changed

- `panel:install --demo` sigue disponible; `--starter` lo amplía con modelo, migración y widget

## [0.26.0] - 2026-06-20

### Added

- **`mobile-bar`** — barra sticky con hamburguesa en modo `sidebar` (móvil)
- Botón **cerrar (X)** en el drawer del sidebar; marca y logo agrupados en el header

### Fixed

- **Layout móvil** en `sidebar`, `topbar` y `dual`: columna flex, menú off-canvas; grid de `dual` solo en desktop
- **Sidebar derecho** — drawer desde la derecha en pantallas pequeñas
- **Hamburguesa duplicada** en `dual`/`topbar` (eliminada del `page-header`)

### Changed

- `footer_links` del sidebar ocultos en móvil; sin enlace automático al playground en el footer
- Escenario del playground alineado con el layout móvil del panel real

## [0.25.0] - 2026-06-20

### Added

- **Playground público** — `GET /playground` (`panel.playground`): personalización en vivo de `config/panel.php` y `ChartWidget` sin login
- Pestañas Inicio, Apariencia, Colores, Gráficos, Tu código y Avanzado; zonas resaltadas («Cambiado aquí»)
- **ChartWidget en playground** — 5 tipos (bar, line, pie, doughnut, progression), estilos moderno/minimal/bold, export PHP con `->options()`
- Comando **`panel:doctor`** — valida config, ruta playground, Livewire navigate, Tailwind, Spatie, vistas publicadas
- Comando **`panel:make-widget`** — stubs chart|stat|resource-count|view en `app/Panel/Widgets/`
- Enlace automático al playground en footer del sidebar cuando `documentation.enabled`
- Aviso en pestaña Gráficos: widgets van en `config/panel.php` → `widgets`
- Tests ampliados: playground Livewire, gráficos, `PanelDoctor`, footer playground

### Fixed

- Gráficos del playground que desaparecían tras commits Livewire (IDs estables + `panelPlaygroundSincronizarGraficos`)
- JSON de configuración de gráficos roto en atributos HTML (`@json` + `getAttribute`)
- `wire:mouseleave` y optional chaining en handlers Livewire del playground

## [0.24.0] - 2026-06-21

### Added

- **Layout modes** — `sidebar` | `topbar` | `dual` + `sidebar_position` left/right
- **PanelSlots** — inyectar vistas en `sidebar.before`, `main.after`, `topbar.end`, etc.
- **Import upsert** — `import.upsert`, `import.upsert_key`, `Resource::importUpsertKey()`
- **Tablas** — `table_striped`, `table_compact`, selector `per_page` en listados
- **Resource hooks** — `navigationBadge()`, `hiddenFromNavigation()`, `perPageOptions()`
- **Tema** — `theme.presets_file` para presets propios en la app
- **`panel:install --demo`** — navigation stub + PostResource ejemplo
- Tests upsert + layout

### Changed

- Títulos HTML con `title_prefix` / `title_suffix`
- Búsqueda global desactivable (`global_search`)

## [0.23.0] - 2026-06-21

### Added

- **`PanelLayout`** — densidad, ancho contenido, sidebar colapsable, breadcrumbs, enlaces footer
- **`auth_ui`** — layout `centered`|`split`, fondo, imagen lateral, tagline de marca
- **`customization`** — CSS inline y vista Blade en `<head>`
- **Marca ampliada** — `favicon`, `logo_height`, `tagline`
- **`RepeaterField`** — filas repetibles con columnas configurables (JSON)
- Tests `PanelLayoutTest`

### Changed

- Sidebar con botón colapsar (escritorio), persistencia en `localStorage`
- Login split con imagen lateral opcional

## [0.22.0] - 2026-06-21

### Added

- **Extensibilidad** — `PanelExtensions` facade: registrar vistas de campos/columnas y widgets desde código o `config/panel.php` → `extensions`
- **Presets de tema** — `theme.preset`: `minimal`, `corporate`, `contrast`, `ocean` (`config/panel-theme-presets.php`)
- **Campos nuevos** — `ColorField`, `DateTimeField`, `KeyValueField`, `CustomField`
- **Columna** — `ColorColumn`
- Comando **`panel:upgrade-views`** — detecta vistas publicadas desactualizadas (`--dry-run`, `--force`)
- Tests `ExtensibilityTest`

### Changed

- `ThemeResolver` resuelve tema vía `ThemePresets` (preset + overrides)
- `WidgetRegistry` fusiona widgets de config y extensiones registradas

## [0.21.0] - 2026-06-21

### Added

- **Suplantación de usuario** — `PanelImpersonation`, config `impersonation`, RowAction «Entrar como», widget en sidebar, ruta `panel.impersonation.leave`
- Tests `PanelImpersonationTest`

### Changed

- **Renombrado del paquete:** `mylaraveltools/minimalist` → `mylaraveltools/panel` (`composer replace` para migración)
- `Package::DISPLAY_NAME` → `Panel`; README reescrito como guía para usuario final
- Widget de suplantación en **sidebar** (encima del perfil), no banner superior

### Fixed

- `TextField` ya no es `final` — `EmailField` puede extenderla

## [0.20.0] - 2026-06-21

### Added

- Partial **`auth-loading-text`** — texto de carga en botones auth con puntos animados (`Entrando` / `Registrando`)
- Claves i18n **`registering`** (ES/EN)

### Changed

- Tras login, registro y verify-email: **redirect completo** (`navigate: false`) — sin loader SPA ni porcentaje tras el POST
- `Package::VERSION` alineado con el changelog (sidebar muestra `v0.20.0`)

### Fixed

- Login percibido como lento: secuencia doble «Entrando…» + «Cargando %»
- Al fallar credenciales, el botón vuelve a su etiqueta normal (`wire:loading` solo durante la petición)

## [0.19.0] - 2026-06-20

### Added

- **Import con vista previa** — `ResourceImporter::analyzePath()`, modal en dos pasos, config `import.preview`
- Tests `ResourceImporterTest`

### Changed

- Demo: Producto con `hasOne` (ficha técnica) y `morphMany` (reseñas polimórficas)
- Reseñas migradas a `reviewable_id` / `reviewable_type`

## [0.18.0] - 2026-06-20

### Added

- **`ViewWidget`** — widgets Blade custom en dashboard (`->columnSpan()`)
- **`ChartWidget::themeColors()`** — colores desde `config('panel.theme.colors')` vía `ThemeResolver::chartColors()`
- Tipo **`progression`** — línea con puntos pulsantes (plugin `panelPulse`)
- Tipos **`doughnut`** y runtime centralizado de gráficos

### Changed

- Gráficos se **destruyen y recrean** al cambiar tema claro/oscuro (`panel-theme-changed`) y en navegación SPA
- Doughnut/pie sin ejes; opciones Chart.js más alineadas al tema
- Documentación demo y README actualizados

### Fixed

- Error Chart.js `"progression" is not a registered controller` (tipo resuelto a `line`)
- Vistas publicadas obsoletas rompían widgets (documentado republicar con `--force`)

## [0.17.0] - 2026-06-20

### Added

- **Import CSV/Excel** — `ResourceImporter`, modal en listados, config `panel.import.enabled`
- **Selector de idioma** — `LocaleSwitcher`, `panel.locales`, sesión `panel.locale`
- **RelationManager** — `hasOne()`, `morphMany()`, `morphToMany()`
- **`ChartWidget`** — gráficos bar/line/pie en dashboard (Chart.js CDN)
- **Verificación email** — `panel.auth.email_verification`, middleware `EnsurePanelEmailVerified`, rutas `/email/verify`

### Changed

- Middleware del panel incluye `EnsurePanelEmailVerified`
- `PanelLocale::resolve()` prioriza sesión del usuario

## [0.16.0] - 2026-06-20

### Changed

- **Rebranding:** paquete Composer `mylaraveltools/minimalist` (antes `gallardev/minimalist`)
- **Breaking:** namespace PHP `Panel\Minimalist` → `MyLaravelTools\Panel` (alineado con `MyLaravelTools\Alertas`)

### Fixed

- Login/auth — transición SPA tras login/registro (`navigate: true` + loader en layout guest); Livewire `Auth\Login`; guest sin Alpine en body
- **`/admin/login` parpadeo / formulario invisible** — `spa-navigation` ya no elimina `.panel-auth-shell` al llegar a rutas auth; loader SPA omitido en destinos auth; brand link sin `wire:navigate`
- **Loader en login a pantalla completa** — `panel-spa-loader--fullscreen` en layout guest; CSS dentro del breakpoint desktop; lock JS durante transición post-login
- **Login credenciales incorrectas** — un solo mensaje: toast Alertas si `mylaraveltools/alertas` está instalado; si no, error en campo email (eliminado resumen duplicado en la vista)
- **Login validación/locale/tema** — `return` tras fallo de auth; locale en requests Livewire; mensajes ES en `PanelValidation`; tema auth reaplicado tras morph Livewire

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
