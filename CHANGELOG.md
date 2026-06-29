# Changelog

All notable changes to `mylaraveltools/panel` are documented in this file.

## [Unreleased]

## [0.53.0] - 2026-06-29

### Added

- **Guía UX del listado** en `AGENTS.md` — capas 1–4, config, atajos, helpers y comandos DX

### Changed

- **README** — roadmap actualizado (v0.46–v0.52), sección «UX del listado» con tabla de config y enlace a AGENTS

## [0.52.0] - 2026-06-29

### Added

- **`PanelConsultas`** — eager load automático desde `BelongsToColumn`, caché de opciones `relationship()` y auditoría N+1/índices
- **`Resource::eagerLoadsForIndex()`** — fusiona `with()` manual y relaciones detectadas en la tabla
- **Caché de opciones** — filtros `SelectFilter`/`MultiSelectFilter` y `BelongsToField` (`filter_options_cache`, TTL configurable)
- **Paginación cursor** — opcional para listados muy grandes (`cursor_pagination`, desactivada por defecto)
- **Comando `panel:audit-rendimiento`** — informe de N+1 e índices sugeridos por resource

### Changed

- **`ResourceQuery`** — `contar`/`ids` sin eager load; `paginate` soporta cursor
- **`panel:doctor`** — aviso si hay resources con posibles N+1
- **`PanelListado`** — texto de rango adaptado a paginación cursor

## [0.51.0] - 2026-06-29

### Added

- **Formularios en listado** — validación inline por campo (`forms.validate_inline`), borrador en `localStorage` (`forms.draft_autosave`), foco automático al abrir (`forms.focus_on_open`)
- **Importación guiada** — paso de progreso y resumen post-import con estadísticas y errores (`import.guided_summary`)
- **Preview acciones bulk** — confirmación con recuento «Vas a aplicar X sobre N registros» (`index.bulk_preview`)

### Changed

- **`ManagesResourceFormModal`** — `updatedForm`, `descartarBorradorFormulario`, evento `panel-borrador-limpiado`
- **`PanelLayout`** — helpers para forms, import guiada y bulk preview

## [0.50.0] - 2026-06-29

### Added

- **Columnas ocultables** — menú con persistencia en `localStorage` (`column-toggle`, `panelColumnas`)
- **Vista rápida** — drawer lateral con Shift+clic o botón ojo (`quick-view-drawer`, `abrirVistaRapida`)
- **Presets de filtros** — guardar/aplicar combinaciones en `localStorage` (`filter-presets`, `panelPresetsFiltros`)
- **Selección global** — «Seleccionar todos los resultados» con límite configurable (`seleccionGlobal`, `bulk_select_all_max`)
- **`ResourceQuery::contar()` / `ids()`** — totales e IDs para selección masiva global

### Changed

- **Barra bulk** — banner para seleccionar todos los resultados filtrados
- **`PanelLayout`** — flags `column_toggle`, `quick_view`, `filter_presets`, `select_all_matching`

## [0.49.0] - 2026-06-29

### Added

- **Barra bulk fija** — acciones masivas en barra inferior al seleccionar filas (`bulk-bar`, `limpiarSeleccion`)
- **Copiar enlace** — comparte URL con filtros/búsqueda (`copy-list-link`)
- **Teclado en tabla** — `↑`/`↓` navegan filas, `Enter` abre editar
- **`PanelListado`** — helper de rango de resultados y objetivos de carga

### Changed

- **RelationPanel** — contador, skeleton/busy, filas clicables y teclado alineados con el listado principal

## [0.48.0] - 2026-06-29

### Added

- **`config/performance`** — debounces, skeleton, loader SPA y animaciones ajustables (`PanelRendimiento`)

### Changed

- **Selects de filtro** — sincronizan con Livewire al instante (animación en paralelo, no después)
- **Feedback de carga** — estado `busy` inmediato; skeleton a 50ms (antes 150ms sin feedback)
- **Búsqueda** — debounce 200ms (antes 300ms); fechas 250ms (antes 400ms)
- **Loader SPA** — mínimo 120ms (antes 280ms fijo); animaciones de filtros/select más cortas

## [0.47.0] - 2026-06-29

### Added

- **Cabecera de tabla fija** — `layout.table_sticky_header` (scroll interno con `thead` sticky)
- **Filas clicables** — clic o Enter abre editar (`layout.index.clickable_rows`, `abrirRegistro`)
- **Vista móvil en tarjetas** — listado en cards bajo `md` (`layout.index.mobile_cards`)

### Changed

- **Filtros colapsables** — se abren al aplicar un filtro; con criterios en URL no se pisan por localStorage

## [0.46.0] - 2026-06-29

### Added

- **Chips de criterios activos** — búsqueda y filtros como etiquetas removibles (`quitarCriterio`)
- **Contador de resultados** — «Mostrando 1–15 de 142» junto al buscador
- **Menú Exportar** — CSV, Excel y PDF en desplegable compacto
- **Atajo de teclado** — `/` o `Ctrl+F` enfoca el buscador del listado

### Changed

- `CriteriosActivosIndex` — helper para etiquetas y valores de chips

## [0.45.1] - 2026-06-29

### Changed

- **Skeleton de tabla** — réplica la tabla real: mismas columnas, filas según `perPage`, cabecera y pie de paginación

## [0.45.0] - 2026-06-29

### Changed

- **Buscador + filtros unificados** — una sola tarjeta: búsqueda siempre visible arriba, filtros colapsables debajo

## [0.44.6] - 2026-06-29

### Fixed

- **Cabecera filtros** — sin borde inferior cuadrado; esquinas superiores alineadas con la tarjeta
- **Carga al filtrar** — skeleton visible en ~150ms al cambiar filtros; `wire:model.live` en selects

## [0.44.5] - 2026-06-29

### Fixed

- **Select en filtros** — desplegable teleportado al `body` para no recortarse por el `overflow: hidden` de la animación del panel

## [0.44.4] - 2026-06-29

### Fixed

- **Panel filtros** — apertura suave con animación CSS `grid` (sin saltos bruscos)
- **Select filtro** — cierre animado completo antes de sincronizar con Livewire; sin flash de opciones
- **Carga tabla** — skeleton y tabla mutuamente excluyentes; filtros solo atenúan la tabla

## [0.44.3] - 2026-06-29

### Fixed

- **Filtros** — sin parpadeo al seleccionar: `wire:ignore` en panel y selects, debounce 300ms, sin skeleton al filtrar
- **Desplegable** — la búsqueda ya no se limpia durante la animación de cierre (evita flash de todas las opciones)

## [0.44.2] - 2026-06-29

### Fixed

- **Filtros** — desplegable anclado al campo (sin teleport), no mueve el layout; pequeño hueco bajo el trigger
- **Tabla** — esquinas inferiores redondeadas (pie y última fila) alineadas con `--panel-form-radius`

## [0.44.1] - 2026-06-29

### Fixed

- **Select buscable** — desplegable teleportado al `body` con posición fija (no se recorta dentro del panel de filtros)
- Placeholder «Todos» en filtros; buscador y lista con mejor padding

## [0.44.0] - 2026-06-29

### Added

- **Select buscable** — componente `<x-panel::searchable-select>` con búsqueda, teclado y opciones con padding/espaciado mejorado
- Aplicado en filtros, formularios, papelera y selector «por página»

## [0.43.1] - 2026-06-29

### Fixed

- **Panel de filtros** — tarjeta unificada al abrir (sin doble borde entre botón y cuerpo); grid más equilibrado
- **Rango de fechas** — campos más anchos, separador visual y foco suavizado en `input[type="date"]`
- Traducciones `date_from` / `date_to` (ES/EN)

## [0.43.0] - 2026-06-29

### Added

- **Filtros colapsables en listados** — `layout.filters.mode` (`collapsible` | `inline`), `default_open`, `remember_state` (localStorage)
- Panel plegable con badge de filtros activos, grid responsive y animación; modo `inline` conserva el diseño anterior
- `PanelLayout::modoFiltros()`, `filtrosAbiertosPorDefecto()`, `recordarEstadoFiltros()`
- Partials `filter-fields.blade.php` y `filters-panel.blade.php`; icono `sliders-horizontal`
- Playground: opciones de filtros en pestaña Apariencia

## [0.42.1] - 2026-06-29

### Fixed

- **Tabla — cabecera redondeada** — `border-collapse: separate` + esquinas superiores en `th` y `overflow: hidden` en el wrap; elimina el efecto de columnas cuadradas dentro de un contenedor redondeado
- **Paginación en una línea** — nuevo `partials/table-footer.blade.php`: «Por página» + selector y controles de página en fila única (`flex-wrap: nowrap`); resumen «Mostrando…» oculto en el pie de tabla

## [0.42.0] - 2026-06-29

### Changed

- **Formularios modernos** — bordes más redondeados vía `--panel-input-radius` y `--panel-form-radius`; radio por defecto `1rem` en `theme.radius`
- Inputs, botones, checkboxes y auth usan el mismo sistema de redondeo configurable
- Tarjeta de formulario (`panel-form-card`) y secciones con sombra suave y más espacio
- Pestañas de formulario estilo *pill* (contenedor redondeado, tab activa elevada)
- Rich-text, repeater y file upload con clases dedicadas (`panel-rich-text-*`, `panel-repeater-item`, `panel-file-input`)
- Modal de formulario cierra con `Escape`

## [0.41.0] - 2026-06-29

### Changed

- **Listados — estado vacío contextual** — `partials/empty-table.blade.php`: mensajes distintos para papelera, búsqueda sin resultados, filtros activos o lista vacía; CTA para crear o limpiar búsqueda/filtros
- **Búsqueda** — botón «×» para limpiar el texto sin borrar otros filtros (`panel-search-clear`)
- **Tablas** — cabecera `position: sticky` al hacer scroll; contenedor `panel-table-scroll` sin `overflow: hidden` en el wrap
- **Accesibilidad** — anillos `focus-visible` en botones, inputs, navegación y acciones de fila
- **Modales** — animación de entrada suave; modal de confirmación cierra con `Escape`

## [0.40.1] - 2026-06-25

### Fixed

- **Error pages — fuente rota** — `--panel-font` se emitía con entidades HTML (`&#039;`) porque Blade escapa `{{ }}` dentro de `<style>`; la fuente del panel nunca cargaba. Cambiado a `{!! $font !!}` (valor de config de servidor, sin riesgo XSS). Ahora `font-family: var(--panel-font)` resuelve correctamente a la fuente configurada

## [0.40.0] - 2026-06-25

### Changed

- **Páginas de error — diseño minimalista** — `layouts/error.blade.php` simplificado al máximo: sin cabecera de marca (icono/nombre de la app), sin tarjeta, sin badge de icono y sin fondo decorativo. Solo código del error, título, descripción y botones, centrados sobre el fondo del panel, con la **misma fuente del panel** (`--panel-font` vía `ThemeResolver`)
- **Iconos vía `x-panel::icon`** — el layout no usa SVGs sueltos: el toggle de tema usa `sun`/`moon` y los botones de acción llevan icono (`arrow-left`, `layout-dashboard`, `rotate-ccw`)

## [0.39.4] - 2026-06-25

### Fixed

- **Error 500 — botón sin traducir** — la vista `errors/500.blade.php` usaba `panel::panel.errors.500.action` pero las traducciones definían `action_retry`; se mostraba la clave cruda al usuario. Renombrada la clave a `action` en `lang/es` y `lang/en`, coherente con 419/429/503

### Added

- **Tests de páginas de error** — `tests/Feature/ErrorPagesTest.php` cubre el render de las 7 vistas (403, 404, 419, 422, 429, 500, 503), que no queden claves sin traducir, que la 503 muestre el mensaje de mantenimiento y que la 500 no filtre el mensaje interno de la excepción

## [0.39.3] - 2026-06-25

### Fixed

- **Error pages — iconos via `x-panel::icon`** — eliminados los SVG inline de las 7 vistas de error; ahora usan `<x-panel::icon name="...">` igual que el resto del panel. Añadidos al componente `icon.blade.php` los iconos Lucide `clock`, `alert-circle`, `alert-triangle`, `zap` y `wrench` que faltaban

## [0.39.2] - 2026-06-25

### Fixed

- **Error pages — iconos y fuente** — iconos cambiados a `stroke-width="2"` con paths Lucide (igual que el componente `x-panel::icon`); normalización tipográfica en `html` (`line-height`, `font-feature-settings`, `-moz-osx-font-smoothing`); `@@media` escapado en Blade; fuente aplicada a `html` además de `body`

## [0.39.1] - 2026-06-25

### Fixed

- **Error layout — brand roto** — `@include('panel::partials.brand-mark')` usaba `panel-brand-mark` (CSS compilado) y `h-4 w-4` (Tailwind); sustituido por SVG inline propio; el layout ya no depende de ningún asset externo

## [0.39.0] - 2026-06-25

### Added

- **Páginas de error con estilo** — 7 vistas (`403, 404, 419, 422, 429, 500, 503`) con el tema del panel
- `layouts/error.blade.php` — layout auto-contenido (sin Vite/Livewire), aplica CSS variables del tema y dark/light mode
- Traducciones `errors.*` en ES y EN con títulos, descripciones y acciones por código
- Publish group `panel-errors` → `php artisan vendor:publish --tag=panel-errors` copia las vistas a `resources/views/errors/`

## [0.38.0] - 2026-06-25

### Fixed

- **Loader SPA pillado** — si `livewire:navigate` falla o tarda (red, 500, sesión/BD), el overlay ya no queda bloqueado: watchdog configurable, `Escape` para cerrar y API `window.panelSpaLoader.ocultar()`
- Navegación cancelada (`defaultPrevented`) ya no muestra el loader

### Added

- `layout.spa_loader` (bool), `layout.spa_loader_timeout_ms` (default 20000), `layout.spa_loader_escape` (default true)

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
