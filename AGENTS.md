# AGENTS — Panel (`mylaraveltools/panel`)

## Estado actual

**Paquete Composer:** `mylaraveltools/panel` — `composer require mylaraveltools/panel` (namespace PHP: `MyLaravelTools\Panel`).

> Migración desde `mylaraveltools/minimalist`: sustituye el require; el namespace PHP no cambia. `composer.json` incluye `replace` para compatibilidad.

**Fase 9** (2026-06-18): páginas custom (`Page`, `panel:make-page`, ruta `pages/{slug}`) e integración opcional de permisos Spatie/Gate (`PanelPermission`, filtrado de navegación).

**Fase 10** (2026-06-19): autenticación integrada — login/registro/logout en `{panel.path}/login` con layout guest y modelo `users` de Laravel.

**Fase 11** (2026-06-19): recuperar contraseña (`ForgotPassword`, `ResetPassword`) y `RolesField`/`RolesColumn` para Spatie.

**Fase 12** (2026-06-19): perfil de usuario — `/admin/profile` (nombre, email, contraseña); enlace en sidebar.

**v0.13.0** (2026-06-19): layout sin header — `<x-panel::page-header>` (título + breadcrumbs en la misma fila); footer del sidebar con tema / versión / logout; loader SPA con `%`.

**v0.14.0** (2026-06-19): resources Spatie integrados — `RoleResource`, `PermissionResource`, `PermissionsField`/`PermissionsColumn`; auto-registro cuando `permissions.enabled` + Spatie instalado.

**v0.17.0** (2026-06-20): Fase 15 — import CSV/Excel, selector de idioma, RelationManager HasOne/Morph, `ChartWidget`, verificación email.

**v0.18.0** (2026-06-20): `ViewWidget`, tipo `progression`, `ChartWidget::themeColors()`, gráficos reactivos al tema y SPA, docs demo al día.

**v0.20.0** (2026-06-21): auth UX — redirect completo post-login, botón con puntos animados (`auth-loading-text`), `Package::VERSION` sincronizado.

**v0.23.0** (2026-06-21): `PanelLayout` — densidad, contenido boxed, sidebar colapsable, auth split, `RepeaterField`, `customization.css`.

**v0.24.0** (2026-06-21): personalización máxima — modos layout (`sidebar`|`topbar`|`dual`), `PanelSlots`, import upsert, tablas rayadas/compactas, selector `per_page`, badges en navegación, `theme.presets_file`, `panel:install --demo`.

**v0.25.0** (2026-06-20): playground público completo (gráficos, zonas, export), `panel:doctor`, `panel:make-widget`.

**v0.26.0** (2026-06-20): layout móvil — `mobile-bar`, drawer sidebar/topbar/dual, UX del menú (X, footer limpio, sin hamburguesa duplicada).

**v0.27.0** (2026-06-23): `panel:scaffold`, `panel:install --starter`, playground Auth interactivo, demo ViewWidget, slots visuales, badge modo layout.

**v0.28.0** (2026-06-23): **multi-panel** — `panels` + `default`, `PanelManager`, `panel_route()`, middleware `SetCurrentPanel`.

**v0.29.0** (2026-06-23): `panel:install --multi`, `panel:doctor` multi-panel, playground slots ampliados (`sidebar.after`, `main.before`, `topbar.end`).

**v0.30.0** (2026-06-23): playground — shell layout (sidebar/topbar/dual), preview import y preview permisos en Avanzado.

**v0.31.0** (2026-06-23): `PanelExtensionesGuia` (campo → columna → widget); smoke `panel-demo` + CI `smoke-panel-demo`.

**v0.32.0** (2026-06-23): `panel:install --saas` — kit tenant + extensiones; `PanelSaasGuia` en playground.

**v0.33.0** (2026-06-23): `panel:upgrade-config`, doctor `panel_route()`, smoke `panel-demo` ampliado.

**v0.34.0** (2026-06-23): doctor config desactualizada; tests install starter/multi; playground en panel-demo; CI smoke 8.2–8.4.

**panel-demo (post-v0.34):** rutas Breeze redirigen a `/admin/*`; smoke ampliado (import CSV, suplantación, redirects auth).

**v0.35.0** (2026-06-23): fix redirect suplantación/logout con Livewire; panel-demo auth unificada.

**v0.36.0** (2026-06-23): playground guías RelationManager + multi-panel.

**v0.37.0** (2026-06-23): `PUBLISHING.md`, `composer release:check`, `panel-demo/DEPLOY.md`, `render.yaml`, workflow release.

**Playground público** (v0.25): ruta `GET /playground` (sin login) — demo interactiva de `config/panel.php` y `ChartWidget`; ver sección **Playground** más abajo (regla obligatoria para agentes).

**Estado del producto (v0.34):** librería **completa para producción**. Ciclo install → upgrade → doctor cubierto.

### Herramientas DX (v0.37.0)

- `composer release:check` — alinea VERSION, CHANGELOG y README antes del tag
- `PUBLISHING.md` — guía Packagist actualizada
- `panel-demo/DEPLOY.md` + `render.yaml` — demo online (Render)
- `.github/workflows/release.yml` — tests al pushear tag `v*`

### Herramientas DX (v0.36.0)

- Playground Avanzado → **Resource (hooks)**: `PanelRelacionesGuia` (hasOne / morphMany / belongsToMany)
- Playground Avanzado → **Multi-panel**: `PanelMultiPanelGuia` + diagrama `/admin` + `/cliente`

### Herramientas DX (v0.35.0)

- `panel:doctor` — detecta config desactualizada y sugiere `panel:upgrade-config`
- Tests `InstallPanelStarterTest`, `InstallPanelMultiTest`
- `panel-demo` con `/playground` activo; smoke en PHP 8.2–8.4

### Herramientas DX (v0.33.0)

- `php artisan panel:upgrade-config` — fusiona claves nuevas del paquete (`--dry-run`, backup `.bak`)
- `panel:doctor` — comprueba `panel_route()` operativa
- Smoke `panel-demo`: import, permisos, página custom

### Herramientas DX (v0.32.0)

- `php artisan panel:install --saas` — Tenant, TenantResource, vistas `panel/saas/*`, extensions + slot + widget
- `PanelSaasGuia` — documentación CLI en playground (Extensiones)

### Herramientas DX (v0.31.0)

- `PanelExtensionesGuia` — receta end-to-end en Avanzado (extensions, campos, widgets)
- Smoke `panel-demo`: `PanelSmokeTest` + job CI `smoke-panel-demo`

### Herramientas DX (v0.30.0)

- Playground: diagrama shell layout en escenario desktop (sidebar / topbar / dual)
- Playground Avanzado: preview interactivo de **Importación** y **Permisos**

### Herramientas DX (v0.29.0)

- `php artisan panel:install --multi` — `panel-admin.php`, `panel-cliente.php`, raíz `panels` en `config/panel.php`
- `panel:doctor` — comprueba paths únicos y recuento de paneles en modo multi
- Playground: marcadores visuales de slots `sidebar.after`, `main.before`, `topbar.end`

### Herramientas DX (v0.28.0)

- **Multi-panel** — `panels` + `default` en config; `PanelManager`, `panel_route()`, middleware `SetCurrentPanel`
- Rutas: 1 panel → `panel.*`; 2+ paneles → `panel.{id}.*`
- Helper global `panel_route()` — cargado vía `helpers.php` + `PanelServiceProvider`; en PHP namespaced usar `\panel_route()`

### Herramientas DX (v0.27.0)

- `php artisan panel:scaffold Nombre --policy --widget=stat|chart|resource-count` — resource + policy + widget opcionales; registra widget en config
- `php artisan panel:install --starter` — PostResource, modelo, migración, PostCountWidget, navigation y parche de config

### Herramientas DX (v0.25.0)

- `php artisan panel:doctor` — `PanelDoctor::diagnosticar()`: config, playground, Livewire navigate, Tailwind, Spatie, vistas publicadas, Alertas
- `php artisan panel:make-widget Nombre --type=chart|stat|resource-count|view` — stub en `app/Panel/Widgets/{Nombre}Widget.php` con `definir()`
- `PanelLayout::enlacesFooter()` — solo `config('panel.layout.footer_links')`; el playground no se inyecta automáticamente

### Personalización máxima (v0.24.0)

- `PanelLayout::modo()` — `sidebar` | `topbar` | `dual`; `posicionSidebar()` left/right
- `PanelSlots` — singleton; slots: `sidebar.before`, `main.after`, `topbar.end`, etc.
- Config: `panel.slots`, `panel.extensions.slots`, `panel.layout.mode`, `table_striped`, `table_compact`, `global_search`, `per_page_options`
- `Resource::navigationBadge()`, `hiddenFromNavigation()`, `importUpsertKey()`, `perPageOptions()`
- Import upsert: `panel.import.upsert`, `panel.import.upsert_key`
- `ThemePresets` — fusiona `theme.presets_file` con presets del paquete
- Vista parcial `partials/topbar.blade.php` + `render-slot.blade.php`
- `php artisan panel:install --demo` — stubs navigation + PostResource
- **Móvil (< lg):** `sidebar` | `topbar` | `dual` usan columna flex; menú lateral off-canvas; barra `mobile-bar` en modo solo-sidebar; hamburguesa en topbar (dual/topbar); sidebar derecho desliza desde la derecha

### Layout y apariencia (v0.23.0)

- `PanelLayout` — `densidad()`, `anchoContenido()`, `sidebarColapsable()`, `enlacesFooter()`, `marca()`, `authUi()`
- Config: `panel.layout`, `panel.auth_ui`, `panel.customization`, `brand.favicon|logo_height|tagline`
- Sidebar colapsable en desktop (`layout.sidebar_collapsible`) + `localStorage`
- Auth split: `auth_ui.layout => split` + `auth_ui.image`
- `RepeaterField::make('items')->columns(['title' => 'Título'])->minRows(1)`

**v0.22.0** (2026-06-21): extensibilidad — `PanelExtensions`, presets de tema, `ColorField`/`DateTimeField`/`KeyValueField`/`CustomField`, `panel:upgrade-views`.

### Extensibilidad (v0.22.0)

- `MyLaravelTools\Panel\Support\PanelExtensions` — singleton + facade `PanelExtensions`
- `registrarVistaCampo($tipo, $vista)` / `registrarVistaColumna($tipo, $vista)` / `registrarWidget($obj)`
- Config: `panel.extensions.field_views`, `column_views`, `widgets`
- `CustomField::make('x')->type('json')->view('mi-app.panel.campo')` — vista Blade propia
- `ThemePresets` — `theme.preset` + overrides en `config/panel.php`
- Presets: `minimal`, `corporate`, `contrast`, `ocean`
- `php artisan panel:upgrade-views` — compara MD5 vendor vs `resources/views/vendor/panel`

**v0.21.0** (2026-06-21): renombre Packagist `mylaraveltools/panel`; suplantación de usuario — `PanelImpersonation`, RowAction «Entrar como», widget sidebar, config `impersonation`.

### Suplantación de usuario (v0.21.0)

- `MyLaravelTools\Panel\Support\PanelImpersonation` — `start()`, `leave()`, `isActive()`, `originalUser()`
- Config: `panel.impersonation` — `enabled`, `permission`, `exclude_ids`, `banner`
- `RowAction::impersonate()` en listados; acción `impersonate` en `ResourceIndex`
- Ruta: `panel.impersonation.leave` (POST)
- UI: partial `impersonation-banner.blade.php` en sidebar (encima del perfil), estilos en `theme-styles.blade.php`
- Permiso por defecto: `impersonate users`; no suplantar al propio usuario

**v0.19.0** (2026-06-20): import con **vista previa** (`import.preview`), demo hasOne/morphMany en Productos.

**v0.16.0** (2026-06-20): namespace PHP `MyLaravelTools\Panel` (antes `Panel\Minimalist`); alineado con `MyLaravelTools\Alertas`.

## Fase 15 — Import, locale, relations, charts, email verify (v0.17.0)

### Import CSV/Excel

- `ResourceImporter::analyzePath()` — vista previa fila a fila (válida / error)
- `ResourceImporter::importPayloads()` — persiste solo filas validadas
- Config: `panel.import.enabled`, `panel.import.preview` (default `true`)
- Modal en dos pasos: subir archivo → tabla preview → confirmar
- `Field::importable(false)` — excluye un campo del form
- `Resource::import()` — esquema propio de columnas (vacío = form filtrado)

### Selector de idioma

- `PanelLocale::resolve()` — sesión `panel.locale` > config `panel.locale`
- Livewire `LocaleSwitcher` en sidebar footer y layout guest
- Config: `panel.locales`, `panel.locale_selector`

### RelationManager ampliado

- `hasOne()`, `morphMany()`, `morphToMany()` — ramas en `RelationPanel::save()`/`delete()`
- `isPivotRelation()` unifica belongsToMany + morphToMany (detach)

### ChartWidget

- `ChartWidget::make($label, 'bar'|'line'|'pie'|'doughnut'|'progression', $data)` — Chart.js con tema del panel
- `progression` — línea suave + puntos parpadeantes (plugin `panelPulse`); render interno como `line`
- `->themeColors()` — claves resueltas en JS vía `--panel-primary`, `--panel-success`, etc. (claro/oscuro)
- `->colors([])`, `->options([])`, `->height(140)` — override manual
- Partial `partials/widget-chart.blade.php` — runtime centralizado (`panelChartMount`, `panelChartRefreshAll`)
- Repinta en `panel-theme-changed` (toggle sidebar) y `livewire:navigated`
- Chart.js CDN solo si hay charts en el dashboard

## Playground público (`/playground`)

Ruta demo **sin autenticación** para que el usuario final pruebe personalización y copie código. Livewire: `MyLaravelTools\Panel\Livewire\PlaygroundApp` (`panel.playground`). Layout: `layouts/playground.blade.php`. Activar/desactivar: `config('panel.documentation.enabled')`.

### Regla obligatoria (agentes y mantenimiento)

**Cada cambio que afecte personalización del panel debe reflejarse también en el playground** cuando sea demostrable:

| Tipo de cambio | Dónde actualizar en el playground |
|----------------|----------------------------------|
| Nueva clave en `config/panel.php` interactiva | `PanelDocumentacion` (sección + `vista_previa: true`), `PanelPlayground`, export PHP, zona en `PanelPlaygroundVista` si hay preview visual |
| Nuevo preset / color / layout / tabla | Pestaña Apariencia o Colores + resaltado de zona en `playground-escenario` / `playground-vista-previa` |
| `ChartWidget` (tipos, estilos, opciones JS) | `PanelPlaygroundGraficos`, `playground-graficos*.blade.php`, `chart-mount-runtime.blade.php`, export `->options()` |
| UI nueva en shell (sidebar, topbar, slots) | Escenario fake `playground-escenario.blade.php` o componente `playground-zona` |
| Solo API PHP / sin preview (comandos, policies) | `PanelDocumentacion::referencia()` en pestaña **Avanzado** |
| Textos de UI del drawer | `lang/es/panel.php` y `lang/en/panel.php` → clave `documentation.*` |
| Estilos del playground | `partials/theme-styles.blade.php` (bloque `/* Playground público */`) |

Checklist antes de cerrar un PR de personalización:

1. ¿El usuario puede **ver** el efecto en la demo? Si no → añadir zona, opción o pestaña.
2. ¿Puede **copiar** código (`Tu código` o bloque PHP del gráfico)?
3. ¿`PanelDocumentacion::clavesInteractivas()` incluye las claves nuevas?
4. Tests en `PanelPlaygroundTest`, `PanelPlaygroundVistaTest`, `PanelPlaygroundGraficosTest`, `Feature/PlaygroundTest` si aplica.
5. En `panel-demo`: `php artisan vendor:publish --tag=panel-views --force` si se tocaron vistas.
6. En cada release: `Package::VERSION`, entrada en `CHANGELOG.md`, roadmap en `README.md`, y esta sección si cambia el alcance del playground.

### Qué incluye hoy el playground

| Pestaña | Contenido |
|---------|-----------|
| **Inicio** | Pasos 1-2-3 + atajos: personalizar, **Probar gráficos** (`go_charts`), ver exportar |
| **Apariencia** | `layout.*`, `brand.*` (menú, marca, modo, densidad, tablas…) |
| **Colores** | `theme.*` (preset, tipografía, paleta) |
| **Gráficos** | 5 tipos ChartWidget en vivo; estilo moderno/minimal/bold; aviso `widgets` en config; copia `ChartWidget::make(...)` |
| **Auth** | `auth_ui.*` interactivo; mini preview login centered/split en escenario |
| **Tu código** | Fragmento `config/panel.php` solo con overrides de sesión |
| **Avanzado** | Nav lateral por sección técnica; opciones vivas + bloque «solo referencia»; guías import, permisos, extensiones, SaaS, **RelationManager**, **multi-panel** |

UX demo:

- Panel **fake** (sin login) a pantalla completa; drawer lateral de controles.
- **Zonas resaltadas** (`PanelPlaygroundVista`): marca, menú, contenido, tabla, acentos, tema, gráficos — badge «Cambiado aquí» al editar.
- Gráficos: `data-panel-chart-config` + `panelPlaygroundSincronizarGraficos()` tras cada commit Livewire (no usar `@push` por canvas suelto).

### Archivos clave

| Archivo | Rol |
|---------|-----|
| `src/Livewire/PlaygroundApp.php` | Estado Livewire, secciones, reinicio |
| `src/Support/PanelPlayground.php` | Sesión `panel.playground`, apply config, export PHP |
| `src/Support/PanelPlaygroundGraficos.php` | Sesión gráficos, `ChartWidget` demo, export código widget |
| `src/Support/PanelDocumentacion.php` | Catálogo secciones, `gruposUsuario()`, claves interactivas |
| `src/Support/PanelPlaygroundVista.php` | Mapa clave → zona visual |
| `resources/views/livewire/playground-app.blade.php` | Drawer + escenario |
| `resources/views/partials/playground-*.blade.php` | Partials UI |
| `resources/views/partials/chart-mount-runtime.blade.php` | Chart.js mount compartido |
| `tests/Unit/PanelPlayground*.php` | Tests sesión, zonas, gráficos |
| `tests/Feature/PlaygroundTest.php` | HTTP público, Livewire, gráficos montados |
| `src/Support/PanelDoctor.php` | Diagnóstico para `panel:doctor` |
| `src/Commands/MakeWidgetCommand.php` | Stub widgets dashboard |

### Completado en v0.25 (ya no pendiente)

- [x] Botón **Probar gráficos** en pestaña Inicio (`go_charts` → pestaña Gráficos)
- [x] Aviso en UI: widgets del dashboard van en `config/panel.php` → `widgets`, no en `theme`
- [x] `panel:doctor` y `panel:make-widget`
- [x] Sincronización release: `CHANGELOG.md`, `README` roadmap, `Package::VERSION` en v0.25.0

### Pendiente opcional (playground / demo)

Mejoras que **no bloquean** el uso del paquete; solo enriquecen la demo pública:

| Área | Qué falta |
|------|-----------|
| **PanelSlots** | ~~Preview de más slots~~ — hecho en v0.29 |
| **Import / permisos** | ~~Preview interactivo~~ — hecho en v0.30 (Avanzado) |
| **Modos layout** | ~~Preview explícito topbar/dual~~ — shell layout en escenario (v0.30) |

### Roadmap producto (fuera del playground)

Definido en `README.md` — no son carencias del core actual:

- [x] **Multi-panel** — `panels`, `default`, `PanelManager`, `panel_route()` (v0.28)
- [x] **Instalador multi** — `panel:install --multi` (v0.29)
- [x] **Starter kit** — `panel:install --starter` + `panel:scaffold` (v0.27)

### Ecosistema (nice-to-have)

- [x] Guía «campo/columna/widget custom» end-to-end — `PanelExtensionesGuia` en playground (v0.31)
- [x] Smoke tests del host (`panel-demo`) en CI — `PanelSmokeTest` + job `smoke-panel-demo` (v0.31)
- [x] Kit SaaS — `panel:install --saas` + `PanelSaasGuia` (v0.32)
- [x] Upgrade config — `panel:upgrade-config` + doctor `panel_route()` (v0.33)
- [x] Doctor config desactualizada + tests install starter/multi + playground panel-demo (v0.34)
- [x] panel-demo: auth unificada (Breeze → panel) + smoke import/suplantación (v0.35 demo)
- [x] Playground RelationManager + multi-panel (v0.36)
- [x] Publicación + demo online — PUBLISHING, DEPLOY, render.yaml (v0.37)

### ViewWidget

- `ViewWidget::make($label, 'panel.widgets.mi-grafica', fn () => [...])` — vista Blade propia
- `->columnSpan(2)` — ocupa más columnas en el grid del dashboard
- Sin Chart.js; ideal para CSS/SVG o librerías externas en la vista

### Import — columnas personalizables

- `Field::importable(false)` — excluye del esquema de import/plantilla
- `Resource::import()` — esquema propio (vacío = form filtrado)
- Tipos excluidos siempre: image, file, password, roles, permissions, rich-text

### Vistas publicadas

- Si existen en `resources/views/vendor/panel/`, **sobreescriben** las del vendor
- Tras actualizar paquete: `vendor:publish --tag=panel-views --force` + `view:clear`
- Sin publicar vistas, el host usa las del paquete directamente (recomendado)

### Verificación email

- Config: `panel.auth.email_verification` (default `false`)
- User debe implementar `MustVerifyEmail`
- Middleware `EnsurePanelEmailVerified` en stack del panel
- Rutas: `panel.verification.notice`, `panel.verification.verify`
- Livewire `Auth\VerifyEmail`

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
- **Loader en auth:** `@persist('panel-spa-loader')` entre guest y app (un solo loader, sin reinicio al entrar); fullscreen vía JS + `body.panel-auth-body`; tiempos más cortos en transición login→panel
- Livewire: mantener `navigate.show_progress_bar = true` en `config/livewire.php` — si es `false`, Livewire añade `data-no-progress-bar` y lanza `Alpine is not defined` al cargar; la barra NProgress se oculta vía CSS (`#nprogress` en theme-styles)
- **`panelApp()` en `<head>`** del layout app — definir antes de `@livewireScripts` para que Alpine resuelva `x-data` en `<body>`
- **BOM UTF-8** — las vistas no deben guardarse con BOM (PowerShell `Set-Content` lo añade); un BOM dentro de `.panel-shell` rompe el CSS Grid y crea hueco superior

## Fase 10 — Autenticación integrada (v0.10.0)

- Rutas: `panel.login`, `panel.register`, `panel.logout`
- Livewire: `MyLaravelTools\Panel\Livewire\Auth\Login`, `Register`
- Layout: `panel::layouts.guest` (mismo tema monocromático)
- Tras login/registro: **redirect completo** (`navigate: false`) — entra al panel sin loader SPA ni doble “Entrando… / Cargando %”
- Botón auth: `wire:loading` + `wire:target="login|register"` → partial `auth-loading-text` (puntos animados); al fallar la petición vuelve el texto normal y se muestran errores
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
- **Fase 15–19** — dashboard widgets, import con preview, selector ES/EN
- **Relaciones demo** — Producto: `hasOne` ficha técnica, `morphMany` reseñas, `belongsToMany` tags
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
- Publicación Packagist — `mylaraveltools/panel` en Packagist (ver `PUBLISHING.md`)

## Fases anteriores

- **Fase 6**: RowAction, confirm modal, skeletons, DateRange/MultiSelect
- **Fase 5**: Excel, búsqueda global, Date/File/RichText, i18n, CI
- **Fase 4**: sections, BelongsToMany, breadcrumbs
- **Fase 3**: RelationManager, widgets, CSV, SPA
