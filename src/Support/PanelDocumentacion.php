<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelDocumentacion
{
    /** @return list<array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>}> */
    public static function secciones(): array
    {
        return [
            self::seccionLayout(),
            self::seccionMarca(),
            self::seccionTema(),
            self::seccionAuthUi(),
            self::seccionPersonalizacion(),
            self::seccionImport(),
            self::seccionRutaIdioma(),
            self::seccionAuth(),
            self::seccionPerfilSuplantacion(),
            self::seccionPermisos(),
            self::seccionNavegacion(),
            self::seccionMultiPanel(),
            self::seccionResources(),
            self::seccionPages(),
            self::seccionExtensiones(),
            self::seccionWidgets(),
            self::seccionCampos(),
            self::seccionComandos(),
        ];
    }

    /** @return list<string> */
    public static function clavesInteractivas(): array
    {
        $claves = [];

        foreach (self::secciones() as $seccion) {
            foreach ($seccion['opciones'] as $opcion) {
                if (($opcion['vista_previa'] ?? false) === true) {
                    $claves[] = (string) $opcion['clave'];
                }
            }
        }

        return array_values(array_unique($claves));
    }

    /** @return list<array{id: string, titulo: string, icono: string, tipo: string, descripcion?: string, secciones?: list<string>}> */
    public static function gruposUsuario(): array
    {
        return [
            [
                'id' => 'inicio',
                'titulo' => 'Inicio',
                'icono' => 'sparkles',
                'tipo' => 'inicio',
            ],
            [
                'id' => 'apariencia',
                'titulo' => 'Apariencia',
                'icono' => 'layers',
                'tipo' => 'opciones',
                'descripcion' => 'Menú, nombre de tu panel y estilo general.',
                'secciones' => ['layout', 'brand'],
            ],
            [
                'id' => 'colores',
                'titulo' => 'Colores',
                'icono' => 'tag',
                'tipo' => 'opciones',
                'descripcion' => 'Tema, tipografía y paleta de colores.',
                'secciones' => ['theme'],
            ],
            [
                'id' => 'auth',
                'titulo' => 'Auth',
                'icono' => 'lock',
                'tipo' => 'opciones',
                'descripcion' => 'Layout del login y pantallas de acceso.',
                'secciones' => ['auth_ui'],
            ],
            [
                'id' => 'graficos',
                'titulo' => 'Gráficos',
                'icono' => 'bar-chart',
                'tipo' => 'graficos',
                'descripcion' => 'Prueba bar, line, pie, doughnut y progression con ChartWidget.',
            ],
            [
                'id' => 'codigo',
                'titulo' => 'Tu código',
                'icono' => 'clipboard-list',
                'tipo' => 'exportar',
            ],
            [
                'id' => 'mas',
                'titulo' => 'Avanzado',
                'icono' => 'settings',
                'tipo' => 'avanzado',
                'descripcion' => 'Todas las opciones y referencia técnica.',
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function opcionesGrupo(string $grupoId): array
    {
        $grupo = collect(self::gruposUsuario())->firstWhere('id', $grupoId);
        if (! is_array($grupo) || ($grupo['tipo'] ?? '') !== 'opciones') {
            return [];
        }

        $opciones = [];
        foreach ($grupo['secciones'] ?? [] as $seccionId) {
            $seccion = collect(self::secciones())->firstWhere('id', $seccionId);
            if (! is_array($seccion)) {
                continue;
            }
            foreach ($seccion['opciones'] as $opcion) {
                if (($opcion['vista_previa'] ?? false) !== true) {
                    continue;
                }
                $opciones[] = $opcion;
            }
        }

        return $opciones;
    }

    public static function etiquetaPorClave(string $clave): ?string
    {
        foreach (self::secciones() as $seccion) {
            foreach ($seccion['opciones'] as $opcion) {
                if (($opcion['clave'] ?? '') === $clave) {
                    return (string) ($opcion['etiqueta'] ?? $clave);
                }
            }
        }

        return null;
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionLayout(): array
    {
        return [
            'id' => 'layout',
            'titulo' => 'Layout',
            'descripcion' => 'Estructura visual del panel: sidebar, topbar, tablas y densidad.',
            'opciones' => [
                self::select('layout.mode', 'Modo de layout', ['sidebar' => 'Sidebar lateral', 'topbar' => 'Solo barra superior', 'dual' => 'Sidebar + barra utilitaria'], 'sidebar', 'Disposición del menú principal.', true),
                self::select('layout.sidebar_position', 'Posición sidebar', ['left' => 'Izquierda', 'right' => 'Derecha'], 'left', 'Solo en modos sidebar y dual.', true),
                self::select('layout.density', 'Densidad', ['comfortable' => 'Cómoda', 'compact' => 'Compacta'], 'comfortable', 'Padding y tamaño de controles.', true),
                self::select('layout.content_width', 'Ancho contenido', ['full' => 'Completo', 'boxed' => 'Caja centrada'], 'full', 'Ancho máximo del contenido principal.', true),
                self::booleano('layout.sidebar_collapsible', 'Sidebar colapsable', false, 'Colapsa a iconos en desktop.', true),
                self::texto('layout.sidebar_collapsed_width', 'Ancho sidebar colapsado', '4.5rem', 'CSS válido (rem, px).', true),
                self::booleano('layout.table_striped', 'Tablas rayadas', false, 'Filas alternas en listados.', true),
                self::booleano('layout.table_compact', 'Tablas compactas', false, 'Menos padding en celdas.', true),
                self::booleano('layout.global_search', 'Búsqueda global', true, 'Modal Cmd/Ctrl+K.', false),
                self::booleano('layout.global_search_shortcut', 'Atajo búsqueda', true, 'Cmd/Ctrl+K.', false),
                self::booleano('layout.show_version', 'Versión en sidebar', true, 'Muestra vX en footer.', false),
                self::booleano('layout.show_breadcrumbs', 'Breadcrumbs', true, 'En page-header.', false),
                self::booleano('layout.show_mobile_menu', 'Menú móvil', true, 'Botón hamburguesa.', false),
                self::texto('layout.title_prefix', 'Prefijo <title>', '', 'Texto antes del título HTML.', false),
                self::texto('layout.title_suffix', 'Sufijo <title>', '', 'Texto después del título HTML.', false),
                self::listaNumeros('layout.per_page_options', 'Opciones por página', [15, 25, 50, 100], 'Valores del selector en listados.', false),
                self::referencia('layout.footer_links', 'Enlaces footer', 'array', '[["label"=>"Ayuda","route"=>"panel.dashboard"]]', 'Enlaces bajo el perfil en sidebar.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionMarca(): array
    {
        return [
            'id' => 'brand',
            'titulo' => 'Marca',
            'descripcion' => 'Logo, nombre y favicon visibles en sidebar y topbar.',
            'opciones' => [
                self::texto('brand.name', 'Nombre', 'Panel', 'Título y cabecera.', true),
                self::texto('brand.logo', 'Logo (URL)', '', 'Ruta o URL del logo. Vacío = icono por defecto.', true),
                self::texto('brand.logo_height', 'Alto logo', '2rem', 'Altura CSS del logo.', true),
                self::texto('brand.favicon', 'Favicon', '', 'Ruta al favicon.', false),
                self::texto('brand.tagline', 'Tagline', '', 'Subtítulo en auth y cabeceras.', false),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionTema(): array
    {
        return [
            'id' => 'theme',
            'titulo' => 'Tema y colores',
            'descripcion' => 'Preset, modo claro/oscuro, tipografía y paleta.',
            'opciones' => [
                self::select('theme.preset', 'Preset', self::mapaPresets(), 'minimal', 'Base antes de overrides en config/panel.php.', true),
                self::select('theme.default', 'Tema por defecto', ['dark' => 'Oscuro', 'light' => 'Claro'], 'dark', 'Al cargar sin preferencia guardada.', true),
                self::texto('theme.font', 'Fuente Google', 'Plus Jakarta Sans', 'Nombre para Google Fonts.', true),
                self::texto('theme.radius', 'Border radius', '0.75rem', 'Redondeo global (--panel-radius).', true),
                self::texto('theme.sidebar_width', 'Ancho sidebar', '16rem', 'Ancho del menú lateral.', true),
                self::color('theme.colors.primary', 'Color primario', '#000000', 'Botones, enlaces activos.', true),
                self::color('theme.colors.accent', 'Color acento', '#525252', 'Detalles secundarios.', true),
                self::color('theme.colors.success', 'Éxito', '#16a34a', 'Estados positivos.', true),
                self::color('theme.colors.danger', 'Peligro', '#dc2626', 'Errores y eliminar.', true),
                self::color('theme.colors.warning', 'Aviso', '#ca8a04', 'Advertencias.', true),
                self::referencia('theme.presets_file', 'Archivo presets', 'string|null', "config_path('panel-theme-presets.php')", 'PHP que devuelve array de presets propios.'),
                self::referencia('theme.colors.*', 'Paleta completa', 'array', 'primary, primary_hover, primary_dark, accent…', 'Ver config/panel.php y documentation/panel/README.md.'),
                self::referencia('theme.light / theme.dark', 'Tokens por modo', 'array', 'bg, surface, card, border, heading…', 'Colores de fondo y texto por tema.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionAuthUi(): array
    {
        return [
            'id' => 'auth_ui',
            'titulo' => 'Auth — apariencia',
            'descripcion' => 'Pantallas login, registro y recuperar contraseña.',
            'opciones' => [
                self::select('auth_ui.layout', 'Layout auth', ['centered' => 'Centrado', 'split' => 'Split con imagen'], 'centered', 'split requiere auth_ui.image.', true),
                self::texto('auth_ui.background', 'Fondo', '', 'URL, asset o gradiente CSS.', true),
                self::texto('auth_ui.image', 'Imagen lateral', '', 'Solo layout split.', true),
                self::booleano('auth_ui.show_tagline', 'Mostrar tagline', true, 'Tagline de brand en auth.', true),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionPersonalizacion(): array
    {
        return [
            'id' => 'customization',
            'titulo' => 'Personalización avanzada',
            'descripcion' => 'CSS inline y vistas extra en <head>.',
            'opciones' => [
                self::textarea('customization.css', 'CSS personalizado', '', 'Inyectado en theme-styles.', false),
                self::referencia('customization.head_view', 'Vista head', 'string|null', "'panel-custom.head'", 'Blade extra en <head>.'),
                self::referencia('slots.*', 'Slots Blade', 'array', 'sidebar.before, main.after, topbar.end…', 'Vistas inyectadas en el layout.'),
                self::referencia('extensions.slots', 'Slots por código', 'array', 'PanelExtensions::registrarSlot()', 'Registro programático.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionImport(): array
    {
        return [
            'id' => 'import',
            'titulo' => 'Importación',
            'descripcion' => 'CSV/Excel en listados de resources.',
            'opciones' => [
                self::booleano('import.enabled', 'Import activo', true, 'Botón Importar.', false),
                self::booleano('import.preview', 'Vista previa', true, 'Paso de validación antes de importar.', false),
                self::booleano('import.upsert', 'Upsert', false, 'Actualiza si existe clave.', false),
                self::texto('import.upsert_key', 'Clave upsert global', '', 'null = usa Resource::importUpsertKey().', false),
                self::referencia('Field::importable()', 'Campo importable', 'bool', 'importable(false) excluye columna', 'Por campo en form().'),
                self::referencia('Resource::import()', 'Esquema import', 'array', 'Columnas custom de import', 'Vacío = form filtrado.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionRutaIdioma(): array
    {
        return [
            'id' => 'ruta',
            'titulo' => 'Ruta e idioma',
            'descripcion' => 'Prefijo URL, locale y middleware.',
            'opciones' => [
                self::texto('path', 'Ruta del panel', 'admin', 'Prefijo URL (/admin).', false),
                self::texto('locale', 'Locale forzado', 'es', 'null = locale de la app.', false),
                self::referencia('locales', 'Idiomas', 'array', "['es'=>'Español','en'=>'English']", 'Clave => etiqueta.'),
                self::booleano('locale_selector', 'Selector idioma', true, 'Globo en sidebar/topbar.', false),
                self::texto('guard', 'Guard', 'web', 'Guard de autenticación.', false),
                self::referencia('middleware', 'Middleware', 'array', 'web, SetPanelLocale, EnsurePanelAccess…', 'Stack de rutas del panel.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionAuth(): array
    {
        return [
            'id' => 'auth',
            'titulo' => 'Autenticación',
            'descripcion' => 'Login/registro integrado o externo.',
            'opciones' => [
                self::booleano('auth.enabled', 'Auth integrada', true, 'false = Breeze/Fortify externo.', false),
                self::booleano('auth.register', 'Registro', true, 'Ruta /register.', false),
                self::booleano('auth.password_reset', 'Reset contraseña', true, '/forgot-password.', false),
                self::booleano('auth.email_verification', 'Verificar email', false, 'Requiere MustVerifyEmail.', false),
                self::texto('auth.register_role', 'Rol al registrar', '', 'Spatie: rol asignado (ej. viewer).', false),
                self::referencia('auth.user_model', 'Modelo User', 'class|null', 'null = config auth', 'Clase del modelo usuario.'),
                self::referencia('login_route / logout_route', 'Rutas externas', 'string', 'panel.login, logout', 'Si auth.enabled=false.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionPerfilSuplantacion(): array
    {
        return [
            'id' => 'perfil',
            'titulo' => 'Perfil y suplantación',
            'descripcion' => 'Perfil de usuario e impersonation.',
            'opciones' => [
                self::booleano('profile.enabled', 'Perfil', true, 'Ruta /profile.', false),
                self::booleano('impersonation.enabled', 'Suplantación', false, 'Entrar como otro usuario.', false),
                self::texto('impersonation.permission', 'Permiso suplantar', 'impersonate users', 'Spatie/Gate.', false),
                self::booleano('impersonation.banner', 'Banner suplantación', true, 'Aviso en sidebar.', false),
                self::referencia('impersonation.exclude_ids', 'IDs excluidos', 'array', '[1, 2]', 'Usuarios no suplantables.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionPermisos(): array
    {
        return [
            'id' => 'permissions',
            'titulo' => 'Permisos',
            'descripcion' => 'Spatie Laravel Permission o Gate.',
            'opciones' => [
                self::booleano('permissions.enabled', 'Permisos activos', false, 'Filtra menú y acciones.', false),
                self::select('permissions.driver', 'Driver', ['spatie' => 'Spatie', 'gate' => 'Gate'], 'spatie', 'Motor de permisos.', false),
                self::texto('permissions.panel_access', 'Acceso panel', 'access panel', 'Permiso mínimo.', false),
                self::booleano('permissions.resources', 'Resources Rol/Permiso', true, 'RoleResource + PermissionResource.', false),
                self::texto('permissions.manage_permission', 'Gestionar usuarios', 'manage users', 'UserResource avanzado.', false),
                self::referencia('policies.auto_register', 'Auto policies', 'bool', 'true', 'Gate::policy por Resource.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionNavegacion(): array
    {
        return [
            'id' => 'navigation',
            'titulo' => 'Navegación',
            'descripcion' => 'Menú lateral/topbar y grupos.',
            'opciones' => [
                self::referencia('navigation', 'Menú manual', 'array|null', "require 'panel-navigation.php'", 'null = auto desde Resources.'),
                self::booleano('navigation_groups_expanded', 'Grupos abiertos', false, 'Todos los grupos expandidos.', false),
                self::referencia('discovery', 'Auto-discovery', 'array', 'path, namespace, enabled', 'Resources en app/Panel/Resources.'),
                self::referencia('pages.discovery', 'Pages discovery', 'array', 'app/Panel/Pages', 'Páginas custom.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionMultiPanel(): array
    {
        return [
            'id' => 'multi_panel',
            'titulo' => 'Multi-panel',
            'descripcion' => 'Varios backoffice en una app (/admin, /cliente…).',
            'opciones' => [
                self::referencia('panels', 'Paneles', 'array', "'admin' => ['path' => 'admin']", 'Vacío = panel único.'),
                self::referencia('default', 'Panel por defecto', 'string', 'admin', 'Contexto sin prefijo en URL.'),
                self::referencia('panel_route()', 'Helper rutas', '—', "panel_route('dashboard', panel: 'cliente')", 'Usar \\panel_route() en namespaces.'),
                self::referencia('panel:install --multi', 'Instalador', '—', 'configs panel-*.php', 'Scaffold multi-panel.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionResources(): array
    {
        return [
            'id' => 'resources',
            'titulo' => 'Resource (hooks)',
            'descripcion' => 'Métodos estáticos en clases Resource.',
            'opciones' => [
                self::hook('form()', 'array', 'Campos del formulario crear/editar.'),
                self::hook('table()', 'array', 'Columnas del listado.'),
                self::hook('detail()', 'array', 'Vista ver registro.'),
                self::hook('import()', 'array', 'Columnas de importación.'),
                self::hook('filters()', 'array', 'Filtros del listado.'),
                self::hook('relations()', 'array', 'RelationManager en vista ver.'),
                self::hook('navigationBadge()', '?string', 'Badge en menú.'),
                self::hook('hiddenFromNavigation()', 'bool', 'Ocultar del menú.'),
                self::hook('importUpsertKey()', '?string', 'Clave única para upsert.'),
                self::hook('perPageOptions()', 'array', 'Opciones paginación.'),
                self::hook('canViewAny/canCreate/…', 'bool', 'Autorización por acción.'),
                self::hook('policy()', '?string', 'Clase Policy Laravel.'),
                self::hook('slug()', 'string', 'Segmento URL (singular por defecto).'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionPages(): array
    {
        return [
            'id' => 'pages',
            'titulo' => 'Pages custom',
            'descripcion' => 'Páginas no-CRUD en /pages/{slug}.',
            'opciones' => [
                self::hook('view()', 'string', 'Vista Blade.'),
                self::hook('data()', 'array', 'Datos para la vista.'),
                self::hook('permission', '?string', 'Permiso Spatie/Gate.'),
                self::hook('slug()', 'string', 'URL /pages/{slug}.'),
                self::hook('label() / icon()', 'string', 'Menú y cabecera.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionExtensiones(): array
    {
        return [
            'id' => 'extensions',
            'titulo' => 'Extensiones',
            'descripcion' => 'Campos, columnas y widgets propios.',
            'opciones' => [
                self::referencia('extensions.field_views', 'Vistas campo', 'array', "['rating'=>'mi.campo']", 'CustomField / tipos propios.'),
                self::referencia('extensions.column_views', 'Vistas columna', 'array', 'Blade por tipo', 'Columnas custom.'),
                self::referencia('extensions.widgets', 'Widgets', 'array', 'StatWidget, ChartWidget…', 'Extra en dashboard.'),
                self::referencia('PanelExtensions::', 'API PHP', '—', 'registrarVistaCampo, registrarSlot…', 'Ver README.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionWidgets(): array
    {
        return [
            'id' => 'widgets',
            'titulo' => 'Widgets dashboard',
            'descripcion' => 'config panel.widgets',
            'opciones' => [
                self::referencia('ResourceCountWidget', 'Contador', 'class', '::make(ProductResource::class)', 'Total de un resource.'),
                self::referencia('StatWidget', 'Estadística', 'class', "->icon('check')", 'Número con callback.'),
                self::referencia('ChartWidget', 'Gráfico', 'class', 'bar|line|pie|doughnut|progression', '->themeColors().'),
                self::referencia('ViewWidget', 'Vista custom', 'class', 'Blade + callback datos', 'HTML propio.'),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionCampos(): array
    {
        return [
            'id' => 'fields',
            'titulo' => 'Fields y Columns',
            'descripcion' => 'Tipos incluidos en el paquete.',
            'opciones' => [
                self::referencia('Fields', 'Campos', '—', 'TextField, EmailField, SelectField, BelongsToField, DateField, DateTimeField, ColorField, KeyValueField, CustomField, FileField, ImageField, RichTextField, RolesField, PermissionsField, RepeaterField…', ''),
                self::referencia('Columns', 'Columnas', '—', 'TextColumn, BooleanColumn, BadgeColumn, BelongsToColumn, ImageColumn, RolesColumn…', ''),
                self::referencia('Filters', 'Filtros', '—', 'SelectFilter, BooleanFilter, DateRangeFilter, MultiSelectFilter', ''),
            ],
        ];
    }

    /** @return array{id: string, titulo: string, descripcion: string, opciones: list<array<string, mixed>>} */
    private static function seccionComandos(): array
    {
        return [
            'id' => 'commands',
            'titulo' => 'Comandos Artisan',
            'descripcion' => 'CLI del paquete.',
            'opciones' => [
                self::referencia('panel:install', 'Instalar', '—', '--demo, --starter, --saas o --multi', ''),
                self::referencia('panel:scaffold', 'Scaffold', '—', 'Resource + policy + widget', ''),
                self::referencia('panel:make-resource', 'Resource', '—', 'Nombre del modelo', ''),
                self::referencia('panel:make-page', 'Page', '—', 'Página custom', ''),
                self::referencia('panel:make-policy', 'Policy', '—', 'Extiende ResourcePolicy', ''),
                self::referencia('panel:upgrade-config', 'Actualizar config', '—', '--dry-run --force', 'Fusiona claves nuevas del paquete.'),
                self::referencia('panel:upgrade-views', 'Vistas', '—', '--dry-run --force', 'Actualizar vendor/panel.'),
            ],
        ];
    }

    /** @return array<string, string> */
    private static function mapaPresets(): array
    {
        $mapa = [];

        foreach (ThemePresets::nombres() as $nombre) {
            $mapa[$nombre] = ucfirst($nombre);
        }

        return $mapa;
    }

    /** @param array<string, string> $valores */
    private static function select(string $clave, string $etiqueta, array $valores, string $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'select',
            'valores' => $valores,
            'defecto' => $defecto,
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    private static function booleano(string $clave, string $etiqueta, bool $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'boolean',
            'defecto' => $defecto,
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    private static function texto(string $clave, string $etiqueta, string $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'text',
            'defecto' => $defecto,
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    private static function textarea(string $clave, string $etiqueta, string $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'textarea',
            'defecto' => $defecto,
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    private static function color(string $clave, string $etiqueta, string $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'color',
            'defecto' => $defecto,
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    /** @param list<int> $defecto */
    private static function listaNumeros(string $clave, string $etiqueta, array $defecto, string $descripcion, bool $vistaPrevia): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'referencia',
            'defecto' => implode(', ', $defecto),
            'descripcion' => $descripcion,
            'vista_previa' => $vistaPrevia,
        ];
    }

    private static function referencia(string $clave, string $etiqueta, string $tipo, string $ejemplo, string $descripcion): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $etiqueta,
            'tipo' => 'referencia',
            'ejemplo' => $ejemplo,
            'tipo_dato' => $tipo,
            'descripcion' => $descripcion,
            'vista_previa' => false,
        ];
    }

    private static function hook(string $clave, string $tipo, string $descripcion): array
    {
        return [
            'clave' => $clave,
            'etiqueta' => $clave,
            'tipo' => 'hook',
            'tipo_dato' => $tipo,
            'descripcion' => $descripcion,
            'vista_previa' => false,
        ];
    }
}
