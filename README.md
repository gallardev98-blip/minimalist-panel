# Minimalist

Panel de administración declarativo y monocromático para Laravel. Alternativa moderna a AdminLTE, basado en **Livewire 3**, **Tailwind CSS** y una API de **Resources** al estilo Filament/Nova.

```bash
composer require gallardev/minimalist
```

## Requisitos

- PHP 8.2+
- Laravel 11, 12 o 13
- Livewire 3.5+
- Tailwind CSS 3+ en la app host

## Instalación

### 1. Composer

```bash
composer require gallardev/minimalist
```

Repositorio local (desarrollo):

```json
{
  "repositories": [
    { "type": "path", "url": "../minimalist-panel-library" }
  ],
  "require": {
    "gallardev/minimalist": "@dev"
  }
}
```

### 2. Publicar e instalar

```bash
php artisan panel:install
```

Esto publica `config/panel.php`, registra rutas en `/admin` y prepara la estructura.

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

### 3. Tailwind (app host)

Incluye las vistas del paquete en `tailwind.config.js`:

```js
content: [
  './resources/views/**/*.blade.php',
  './vendor/gallardev/minimalist/resources/views/**/*.blade.php',
],
```

Activa modo oscuro por clase:

```js
darkMode: 'class',
```

### 4. Alpine + Livewire

En `resources/js/app.js`, **no** llames `Alpine.start()` en rutas del panel (Livewire lo gestiona):

```js
import Alpine from 'alpinejs';
window.Alpine = Alpine;

if (! window.location.pathname.startsWith('/admin')) {
    Alpine.start();
}
```

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
use Panel\Minimalist\Pages\Page;

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

Botón en el header. Persistencia en `localStorage` (`panel-theme`).

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
],
```

- `EnsurePanelAccess` exige el permiso `panel_access` para entrar al panel
- En **Pages**: `protected static ?string $permission = 'view reports'`
- En **navegación**: `'permission' => 'manage settings'` en enlaces manuales
- Resources y Pages en el menú se ocultan si el usuario no tiene acceso

Usa Spatie en tus Policies: `$user->can('manage products')`.

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

Genera `App\Policies\ProductPolicy` extendiendo `Panel\Minimalist\Policies\ResourcePolicy`.

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
use Panel\Minimalist\Forms\Section;

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
- Sidebar y header persistentes (`@persist`)
- Spinner con blur solo en el área de contenido (`#panel-main`)
- Prefetch con `wire:navigate.hover`

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
| `php artisan panel:make-policy Name` | Crear Policy para un modelo |
| `php artisan vendor:publish --tag=panel-config` | Publicar config |
| `php artisan vendor:publish --tag=panel-views` | Publicar vistas |

---

## Demo

Proyecto de prueba en `panel-demo/`:

```
admin@panel.test / password → /admin
```

---

## RowAction (acciones por fila)

```php
use Panel\Minimalist\Actions\RowAction;

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

## Breadcrumbs con título de registro

```php
protected static ?string $recordTitleAttribute = 'name';
// o automático desde la primera columna searchable
```

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
use Panel\Minimalist\Fields\DateField;
use Panel\Minimalist\Fields\FileField;
use Panel\Minimalist\Fields\RichTextField;

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
use Panel\Minimalist\Forms\Section;
use Panel\Minimalist\Forms\Tab;

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

## Publicar en Packagist

Ver [PUBLISHING.md](PUBLISHING.md) para subir el paquete a Packagist y etiquetar releases.

## Breadcrumbs

Automáticos en el header según la ruta (`Dashboard / Productos / Editar`).

## Tests

```bash
cd minimalist-panel-library
composer test
```

---

## Roadmap

- [x] Fases 1–5 (CRUD, SPA, Excel, búsqueda global, i18n, tests, CI)
- [x] Fase 6: RowAction, confirm modal, skeletons, DateRange/MultiSelect, breadcrumbs con título
- [x] Fase 7: crear/editar en modal, tabs en formularios, export PDF
- [x] Fase 8: Policies Laravel, `panel:make-policy`, `ResourcePolicy`
- [ ] Publicación Packagist — ver `PUBLISHING.md`

## Licencia

MIT
