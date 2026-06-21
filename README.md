# Minimalist

Panel de administración declarativo y monocromático para Laravel. Alternativa moderna a AdminLTE, basado en **Livewire 3**, **Tailwind CSS** y una API de **Resources** al estilo Filament/Nova.

```bash
composer require mylaraveltools/minimalist
```

## Requisitos

- PHP 8.2+
- Laravel 11, 12 o 13
- Livewire 3.5+
- Tailwind CSS 3+ en la app host

## Instalación

### 1. Composer

```bash
composer require mylaraveltools/minimalist
```

Repositorio local (desarrollo):

```json
{
  "repositories": [
    { "type": "path", "url": "../minimalist-panel-library" }
  ],
  "require": {
    "mylaraveltools/minimalist": "@dev"
  }
}
```

### 2. Publicar e instalar

```bash
php artisan panel:install
```

Esto publica `config/panel.php`, registra rutas en `/admin` y prepara la estructura. También publica `config/livewire.php` si no existe.

El panel incluye **login y registro** en `/admin/login` y `/admin/register` usando la tabla `users` de Laravel. No necesitas Breeze salvo que quieras auth separada.

```php
// config/panel.php
'auth' => [
    'enabled' => true,
    'register' => true,
    'register_role' => 'viewer', // opcional, con Spatie HasRoles
],
```

Con auth externa: `'enabled' => false` y `'login_route' => 'login'`.

Tras login/registro el panel hace **recarga completa** al dashboard (no loader SPA). El botón muestra «Entrando» con puntos animados solo mientras dura el POST; si falla, vuelve a «Entrar» y se muestran los errores.

Recuperar contraseña (activo por defecto):

```php
'auth' => [
    'password_reset' => true, // /admin/forgot-password
],
```

### Perfil de usuario

Ruta `/admin/profile` — el usuario logueado edita su cuenta:

```php
'profile' => [
    'enabled' => true,
],
```

Desactivar con `'enabled' => false` si no lo necesitas.

### 3. Tailwind (app host)

Incluye las vistas del paquete en `tailwind.config.js`:

```js
content: [
  './resources/views/**/*.blade.php',
  './vendor/mylaraveltools/minimalist/resources/views/**/*.blade.php',
],
```

Activa modo oscuro por clase:

```js
darkMode: 'class',
```

### 4. Alpine + Livewire

En `resources/js/app.js`, **no importes Alpine en rutas del panel** (`/admin/*`). Livewire lo incluye y arranca solo:

```js
const panelPath = '/admin';

if (! window.location.pathname.startsWith(panelPath)) {
    import('alpinejs').then(({ default: Alpine }) => {
        window.Alpine = Alpine;
        Alpine.start();
    });
}
```

Importar `alpinejs` en `/admin/login` rompe `wire:submit` (el formulario no envía nada).

**APP_URL** debe coincidir con tu servidor de desarrollo (host **y** puerto), p. ej. `http://127.0.0.1:8000`.

---

## Primer Resource

```bash
php artisan panel:make-resource Product --model=Product
```

```php
// app/Panel/Resources/ProductResource.php
final class ProductResource extends Resource
{
    protected static string $model = Product::class;
    protected static ?string $label = 'Productos';
    protected static ?string $icon = 'package'; // icono Lucide

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

Auto-discovery en `app/Panel/Resources/` (configurable en `config/panel.php`).

---

## Configuración (`config/panel.php`)

| Clave | Descripción | Default |
|-------|-------------|---------|
| `path` | Prefijo URL del panel | `admin` |
| `middleware` | Middleware de rutas | `web` + `EnsurePanelAccess` |
| `guard` | Guard de autenticación | `web` |
| `brand.name` | Nombre en sidebar | `Panel` |
| `brand.logo` | URL o ruta del logo (`null` = icono por defecto) | `null` |
| `per_page` | Registros por página | `15` |
| `discovery` | Auto-discovery de Resources | `enabled` |
| `pages` | Auto-discovery de Pages custom | `enabled` |
| `permissions` | Spatie/Gate (`enabled`, `panel_access`) | `disabled` |
| `navigation` | Menú lateral personalizado (`null` = auto desde resources) | `null` |
| `widgets` | Widgets del dashboard | `[]` |

### Navegación con grupos desplegables

Define `navigation` en `config/panel.php` o en un archivo dedicado:

```php
// config/panel.php
'navigation' => require __DIR__.'/panel-navigation.php',
```

Formato de ítems:

```php
return [
    // Enlace a un Resource (resuelve label, icono y URL automáticamente)
    ['resource' => ProductResource::class],

    // Enlace a una Page custom (informes, ajustes)
    ['page' => SettingsPage::class],

    // Enlace manual
    [
        'label' => 'Informe de ventas',
        'icon' => 'bar-chart',
        'route' => 'panel.dashboard',   // preferido (se resuelve en runtime)
        'badge' => 'Demo',              // opcional
    ],

    // Grupo desplegable (Alpine.js)
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

- Los grupos se abren automáticamente si contienen la ruta activa.
- La búsqueda global (`Cmd/Ctrl+K`) indexa todos los enlaces (aplanando grupos).
- Iconos: nombres Lucide soportados en `resources/views/components/icon.blade.php`.
- **No uses `route()` al cargar el config**; usa la clave `route` para enlaces nombrados.

### Configuración (`config/panel.php`)

Tras `panel:install`, edita `config/panel.php`. **No hace falta usar variables `.env`** — todo vive en el archivo de config (buena práctica Laravel; compatible con `config:cache`).

```php
'path' => 'admin',

'brand' => [
    'name' => 'Mi Panel',
    'logo' => '/images/logo.svg',
],

'theme' => [
    'default' => 'dark',
    'colors' => [
        'primary' => '#000000',
        'primary_dark' => '#ffffff',
        'accent' => '#525252',
    ],
],
```

Si prefieres `.env`, puedes envolver valores en el config publicado: `'path' => env('PANEL_PATH', 'admin')`.

---

## Páginas custom

Para informes, ajustes o pantallas que no son CRUD:

```bash
php artisan panel:make-page Settings
```

```php
use MyLaravelTools\Panel\Pages\Page;

final class SettingsPage extends Page
{
    protected static ?string $label = 'General';
    protected static ?string $slug = 'settings-general';
    protected static ?string $permission = 'manage settings';

    public static function view(): string
    {
        return 'panel.pages.settings-general';
    }

    public static function data(): array
    {
        return ['storeName' => config('app.name')];
    }
}
```

- Ruta: `/admin/pages/{slug}`
- Auto-discovery en `app/Panel/Pages`
- Añade al menú: `['page' => SettingsPage::class]`

Vista Blade con cabecera unificada (título + miga de pan en la misma fila):

```blade
<x-panel::page-header class="mb-8">
    <h1>{{ $pageClass::label() }}</h1>
    <p class="panel-muted mt-1 text-sm">Descripción opcional.</p>
</x-panel::page-header>
```

---

## Layout y cabecera de página

Desde **v0.13.0** no hay barra superior global. Cada pantalla usa `<x-panel::page-header>`:

- **Izquierda:** título (y subtítulo, enlace «volver», etc.)
- **Derecha:** miga de pan automática
- **Móvil:** botón menú a la izquierda del título

El **sidebar** incluye en el footer: enlace al perfil, toggle claro/oscuro, versión del panel (`panel.version`) e icono de cerrar sesión.

```php
// config/panel.php — versión mostrada en el sidebar (null = v{Package::VERSION})
'version' => null,
```

---

## Tema y colores

Paleta **monocromática** por defecto (blanco/negro). Totalmente personalizable vía config.

### Estructura

```php
'theme' => [
    'default' => 'dark',           // dark | light
    'font' => 'Plus Jakarta Sans',
    'radius' => '0.75rem',
    'sidebar_width' => '16rem',

    'colors' => [
        'primary' => '#000000',              // modo claro: botones, acentos
        'primary_hover' => '#262626',
        'primary_dark' => '#ffffff',         // modo oscuro
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
```

Los colores se inyectan como variables CSS (`--panel-primary`, `--panel-bg`, etc.) mediante `ThemeResolver`. El contraste del texto en botones primarios se calcula automáticamente.

### Toggle claro/oscuro

Botón en el **footer del sidebar**. Persistencia en `localStorage` (`panel-theme`).

### Clases semánticas

| Clase | Uso |
|-------|-----|
| `.panel-body` | Fondo y texto base |
| `.panel-card` | Tarjetas |
| `.panel-heading` / `.panel-text` / `.panel-muted` | Tipografía |
| `.panel-input` | Formularios |
| `.panel-btn-primary` | Botón principal |
| `.panel-nav-link-active` | Nav activo |
| `.panel-table` | Tablas |

---

## Iconos (Lucide)

```php
protected static ?string $icon = 'users';
```

```blade
<x-panel::icon name="package" class="h-4 w-4" />
```

Disponibles: `layout-dashboard`, `package`, `folder`, `users`, `plus`, `pencil`, `trash-2`, `eye`, `search`, `download`, `layers`, `check-circle`, `loader-2`, etc.

---

## Permisos

### Spatie Laravel Permission (opcional)

```bash
composer require spatie/laravel-permission
```

```php
// config/panel.php
'permissions' => [
    'enabled' => true,
    'panel_access' => 'access panel',
    'resources' => true,              // RoleResource + PermissionResource integrados
    'manage_permission' => 'manage users', // permiso para CRUD roles/permisos
],
```

- `EnsurePanelAccess` exige el permiso `panel_access` para entrar al panel
- Con `resources => true` y Spatie instalado, se registran **`RoleResource`** y **`PermissionResource`** en `/admin/resources/roles` y `/admin/resources/permissions`
- Asigna permisos a roles con `PermissionsField`; asigna roles a usuarios con `RolesField`
- Si defines tus propios resources con slug `roles` o `permissions` en `app/Panel/Resources`, prevalecen sobre los integrados
- En **Pages**: `protected static ?string $permission = 'view reports'`
- En **navegación**: `'permission' => 'manage settings'` en enlaces manuales
- Resources y Pages en el menú se ocultan si el usuario no tiene acceso

Usa Spatie en tus Policies: `$user->can('manage products')`.

### Roles en usuarios

Con Spatie instalado y `HasRoles` en tu modelo `User`:

```php
use MyLaravelTools\Panel\Fields\RolesField;
use MyLaravelTools\Panel\Columns\RolesColumn;

RolesField::make('roles')->label('Roles'),
RolesColumn::make('roles')->label('Roles'),
```

Los roles se sincronizan con `syncRoles()` al crear o editar (no van en `$fillable`).

### Permisos en roles

```php
use MyLaravelTools\Panel\Fields\PermissionsField;
use MyLaravelTools\Panel\Columns\PermissionsColumn;

PermissionsField::make('permissions')->label('Permisos'),
PermissionsColumn::make('permissions')->label('Permisos'),
```

Los permisos se sincronizan con `syncPermissions()` al guardar el rol.

---

Dos capas en **Resources** combinadas con **AND** (ambas deben permitir):

1. **Hooks en el Resource** — `canViewAny()`, `canCreate()`, `canEdit()`, etc.
2. **Policy de Laravel** — si existe para el modelo

### Hooks rápidos

```php
public static function canViewAny(): bool { return true; }
public static function canCreate(): bool { return true; }
public static function canEdit(Model $record): bool { return true; }
public static function canDelete(Model $record): bool { return true; }
```

### Policies (recomendado)

```bash
php artisan panel:make-policy Product
```

Genera `App\Policies\ProductPolicy` extendiendo `MyLaravelTools\Panel\Policies\ResourcePolicy`.

Las policies hijas deben mantener `$user` y `$record` **sin type-hint** (restricción de PHP al heredar). Usa `instanceof` en el cuerpo si necesitas tu modelo:

```php
public function delete($user, $record): bool
{
    return $user instanceof User && $user->isPanelAdmin();
}
```

```php
use App\Policies\ProductPolicy;

protected static ?string $policy = ProductPolicy::class;
```

Sin `$policy` explícita, auto-detecta `App\Policies\{Model}Policy` si `panel.policies.auto_register` es `true`. La base niega todo por defecto.

---

## Filtros

```php
public static function filters(): array
{
    return [
        SelectFilter::make('category_id')
            ->label('Categoría')
            ->relationship(Category::class, 'name'),
        BooleanFilter::make('is_active')->label('Activo'),
    ];
}
```

---

## Acciones masivas

```php
public static function bulkActions(): array
{
    return [
        BulkAction::make('delete', 'Eliminar')
            ->action(fn ($records) => $records->each->delete())
            ->color('rose')
            ->requiresConfirmation(),
    ];
}
```

Incluye `exportSelection` para CSV.

---

## Soft deletes

Si el modelo usa `SoftDeletes`, el listado muestra papelera, restaurar y eliminar permanente.

---

## Vista detalle

```php
public static function detail(): array
{
    return static::table(); // o columnas propias
}
```

Ruta: `GET /admin/resources/{slug}/{id}`

---

## Form Sections

```php
use MyLaravelTools\Panel\Forms\Section;

public static function form(): array
{
    return [
        Section::make('Información', [
            TextField::make('name')->required(),
        ])->description('Datos básicos'),
        TextField::make('email')->required(),
    ];
}
```

## BelongsToMany

```php
public static function relations(): array
{
    return [
        RelationManager::belongsToMany('tags', TagResource::class)
            ->title('Etiquetas'),
    ];
}
```

En la vista **Ver** del registro padre: crear etiqueta y vincular, o desvincular.

## RelationManager (HasMany)

Gestiona relaciones **HasMany** desde la vista detalle:

```php
public static function relations(): array
{
    return [
        RelationManager::make('products', ProductResource::class)
            ->title('Productos de esta categoría'),
    ];
}
```

---

## Widgets

```php
// config/panel.php
'widgets' => [
    ResourceCountWidget::make(ProductResource::class),
    StatWidget::make('Activos', fn () => Product::where('is_active', true)->count())
        ->icon('check-circle'),
],
```

---

## Export CSV

- Botón **Exportar CSV** en listados (respeta búsqueda y filtros)
- Acción masiva **Exportar selección**

---

## Navegación SPA

- `wire:navigate` en enlaces internos
- Sidebar persistente (`@persist` en toasts)
- Loader a pantalla completa del área de contenido con **porcentaje entero** (`0%`–`100%`) en el anillo
- Prefetch con `wire:navigate.hover` (páginas cacheadas saltan a `100%`)

Mantén `show_progress_bar => true` en Livewire (obligatorio). La barra NProgress de Livewire se oculta vía CSS del panel; usar `false` provoca `Alpine is not defined` al cargar `/admin`:

```php
// config/livewire.php
'navigate' => [
    'show_progress_bar' => true,
],
```

---

## Fields disponibles

`TextField`, `EmailField`, `PasswordField`, `TextareaField`, `BooleanField`, `SelectField`, `BelongsToField`, `NumberField`, `ImageField`

## Columns disponibles

`TextColumn`, `BooleanColumn`, `DateTimeColumn`, `BadgeColumn`, `BelongsToColumn`, `ImageColumn`

---

## Comandos

| Comando | Descripción |
|---------|-------------|
| `php artisan panel:install` | Instalar panel |
| `php artisan panel:make-resource Name` | Crear Resource |
| `php artisan panel:make-page Name` | Crear página custom |
| `php artisan panel:make-policy Name` | Crear Policy para un modelo |
| `php artisan vendor:publish --tag=panel-config` | Publicar config |
| `php artisan vendor:publish --tag=panel-views` | Publicar vistas Blade |

Tras actualizar el paquete, republica vistas si las personalizaste en `resources/views/vendor/panel/`:

```bash
php artisan vendor:publish --tag=panel-views --force
php artisan view:clear
```

Si no publicaste vistas, Laravel usa las del vendor directamente (recomendado hasta que necesites editar Blade).

---

## Demo

Proyecto de prueba en `panel-demo/` (ver su `README.md`):

| Usuario | Email | Password |
|---------|-------|----------|
| Admin | `admin@panel.test` | `password` |
| Editor | `editor@panel.test` | `password` |

Dashboard con `ChartWidget` (progresión, doughnut), `ViewWidget` custom e import CSV en productos.

---

## RowAction (acciones por fila)

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

Por defecto: `view`, `edit`, `delete` (+ `restore` / `forceDelete` con soft deletes).

## Breadcrumbs

Automáticos según la ruta (`Panel / Productos / Editar`). Se renderizan dentro de `<x-panel::page-header>` a la derecha del título.

Título del registro en show/edit:

```php
protected static ?string $recordTitleAttribute = 'name';
// o automático desde la primera columna searchable
```

---

## Tests

```bash
cd minimalist-panel-library
composer test
```

Incluye tests de layout SPA (`SpaLoaderTest`): markup del loader con `%`, script de progreso y `page-header`.

## Filtros avanzados

```php
DateRangeFilter::make('published_at')->label('Publicado entre'),
MultiSelectFilter::make('category_id')->relationship(Category::class, 'name'),
```

## Export Excel

```php
// En el listado: botones "Exportar CSV" y "Exportar Excel"
// Bulk actions por defecto incluyen exportSelection y exportSelectionExcel
```

Requiere `phpoffice/phpspreadsheet` (incluido en el paquete).

## Búsqueda global

- **Cmd+K** / **Ctrl+K** abre la paleta de búsqueda
- Busca en navegación y registros (columnas `searchable()`)
- Componente: `panel.global-search`

## Nuevos Fields

```php
use MyLaravelTools\Panel\Fields\DateField;
use MyLaravelTools\Panel\Fields\FileField;
use MyLaravelTools\Panel\Fields\RichTextField;

DateField::make('published_at')->label('Publicación')->time(),
FileField::make('brochure')->directory('docs')->acceptedMimes(['pdf']),
RichTextField::make('description')->label('Descripción'),
```

## Internacionalización

```php
__('panel::panel.save')
```

Traducciones en `lang/es/panel.php` y `lang/en/panel.php` (namespace `panel::panel.*`).

## Formularios en modal

Por defecto, crear y editar se abren en un modal sobre el listado sin salir de la página:

```php
// config/panel.php
'forms_in_modal' => true,
```

Con `false`, se usan las rutas de página completa (`panel.resources.create` / `edit`).

## Tabs en formularios

Organiza secciones en pestañas con `Tab::make()`:

```php
use MyLaravelTools\Panel\Forms\Section;
use MyLaravelTools\Panel\Forms\Tab;

public static function form(): array
{
    return [
        Tab::make('General', [
            Section::make('Datos', [
                TextField::make('name')->required(),
            ]),
        ]),
        Tab::make('Precio', [
            NumberField::make('price')->required(),
        ]),
    ];
}
```

## Export PDF

Botones **CSV**, **XLSX** y **PDF** en la toolbar del listado. Si hay filas seleccionadas, exportan solo la selección; si no, el listado filtrado completo.

## Import CSV / Excel

Botón **Importar** en la toolbar del listado (requiere permiso `create`).

Con **`import.preview` => true** (por defecto), al subir el archivo se muestra una **vista previa**: filas válidas, errores por fila y confirmación antes de guardar.

```php
'import' => [
    'enabled' => true,
    'preview' => true,  // false = importación directa (comportamiento anterior)
],
```

En el modal:
- **Plantilla CSV / Excel** — descarga cabeceras + hasta 5 filas de ejemplo desde la BBDD
- Sube el archivo rellenado (`.csv`, `.txt`, `.xlsx`, `.xls`)

Campos excluidos por tipo: image, file, password, roles, permissions, rich-text

Personaliza qué columnas entran en la plantilla e importación:

```php
// Opción 1 — desactivar campos concretos del form()
TextField::make('sku')->label('SKU'),
DateField::make('published_at')->importable(false),

// Opción 2 — esquema de importación propio (solo estas columnas)
public static function import(): array
{
    return [
        TextField::make('name')->label('Nombre')->required(),
        NumberField::make('price')->label('Precio'),
        BelongsToField::make('category_id')->relationship(Category::class, 'name'),
    ];
}
```

```php
// config/panel.php
'import' => ['enabled' => true],
```

## Selector de idioma

```php
'locale' => 'es',
'locales' => ['es' => 'Español', 'en' => 'English'],
'locale_selector' => true,
```

Icono globo en el footer del sidebar (y en auth). La preferencia se guarda en sesión (`panel.locale`).

## RelationManager — HasOne y Morph

```php
RelationManager::hasOne('profile', ProfileResource::class),
RelationManager::morphMany('comments', CommentResource::class),
RelationManager::morphToMany('tags', TagResource::class),
```

En `panel-demo`, abre un **Producto** → verás **Ficha técnica** (hasOne) y **Reseñas de clientes** (morphMany).

## Widgets con gráficos

### ChartWidget (Chart.js con tema del panel)

```php
use MyLaravelTools\Panel\Widgets\ChartWidget;

ChartWidget::make('Ventas mensuales', 'bar', fn () => [
    'labels' => ['Ene', 'Feb', 'Mar'],
    'values' => [12, 19, 8],
])
    ->color('emerald')
    ->height(160);

ChartWidget::make('Estado', 'doughnut', fn () => [
    'labels' => ['Activos', 'Inactivos'],
    'values' => [10, 2],
])->themeColors();                    // success + danger del tema

ChartWidget::make('Crecimiento', 'progression', fn () => [
    'labels' => ['Ene', 'Feb', 'Mar', 'Abr'],
    'values' => [10, 14, 13, 22],
])->themeColors()->height(160);      // línea + puntos pulsantes

ChartWidget::make('Por canal', 'bar', fn () => [...])->themeColors(['primary', 'accent', 'success']);
```

Tipos: `bar`, `line`, `pie`, `doughnut`, `progression`. Con `->themeColors()` los colores salen de `config('panel.theme.colors')` (doughnut binaria → success/danger). Sin colores, línea/progresión usa `--panel-primary`. Override manual: `->colors([...])`, `->height()`, `->options()`.

Los gráficos **se repintan** al cambiar tema claro/oscuro y al volver al dashboard vía navegación SPA (`panel-theme-changed`, `livewire:navigated`).

### ViewWidget (gráficas propias)

Para diseños totalmente custom (SVG, CSS, ApexCharts, etc.) crea una vista Blade y regístrala:

```php
use MyLaravelTools\Panel\Widgets\ViewWidget;

ViewWidget::make('Salud del catálogo', 'panel.widgets.catalog-health', fn () => [
    'total' => 120,
    'items' => [['name' => 'Moda', 'pct' => 42]],
])->columnSpan(2),
```

La vista recibe `$label` más los datos del closure. Chart.js solo se carga si hay `ChartWidget` en el dashboard.

## Verificación de email

Requiere `MustVerifyEmail` en tu modelo `User`:

```php
'auth' => [
    'email_verification' => true,
],
```

Tras registrarse, el usuario recibe el correo y debe verificar antes de acceder al panel (`/admin/email/verify`).

## Publicar en Packagist

Ver [PUBLISHING.md](PUBLISHING.md) para subir el paquete a Packagist y etiquetar releases.

---

## Roadmap

- [x] Fases 1–5 (CRUD, SPA, Excel, búsqueda global, i18n, tests, CI)
- [x] Fase 6: RowAction, confirm modal, skeletons, DateRange/MultiSelect, breadcrumbs con título
- [x] Fase 7: crear/editar en modal, tabs en formularios, export PDF
- [x] Fase 8: Policies Laravel, `panel:make-policy`, `ResourcePolicy`
- [x] Fase 9: páginas custom (`Page`) y permisos Spatie/Gate
- [x] Fase 10: autenticación integrada (login, registro, logout)
- [x] Fase 11: recuperar contraseña, `RolesField` / `RolesColumn`
- [x] Fase 12: perfil de usuario (`/admin/profile`)
- [x] Fase 13: layout sin header, `<x-panel::page-header>`, loader SPA con `%`
- [x] Fase 14: `RoleResource` / `PermissionResource` integrados, `PermissionsField` / `PermissionsColumn`
- [x] Fase 15: import CSV/Excel, selector de idioma, HasOne/Morph relations, `ChartWidget`, verificación email
- [x] Post-15: `ViewWidget`, `progression`, `themeColors()`, gráficos reactivos al tema/SPA
- [x] **v0.20** — auth UX (redirect post-login, carga animada en botón)
- [x] Packagist — `mylaraveltools/minimalist`

## Licencia

MIT
