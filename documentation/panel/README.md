# Documentación del panel (`mylaraveltools/panel`)

Referencia **completa** de personalización del paquete.

## Vista interactiva (recomendado)

**Pública, sin login, pantalla completa:**

```
http://tu-app.test/playground
```

Configura la ruta con `documentation.path` (por defecto `playground`).

- **Izquierda:** controles de toda la personalización (pestañas por área)
- **Derecha:** panel **falso** a tamaño real (sidebar, topbar, tablas demo)
- Botón **◀** oculta controles → panel demo a pantalla completa
- Cambios **en vivo** en opciones marcadas «En vivo» (solo sesión, no escribe `config/panel.php`)

Publicar esta carpeta en tu proyecto:

```bash
php artisan vendor:publish --tag=panel-documentation
```

---

## Índice de `config/panel.php`

| Sección | Claves principales |
|---------|-------------------|
| Ruta | `path`, `locale`, `locales`, `locale_selector`, `guard`, `middleware` |
| Auth | `auth.enabled`, `register`, `password_reset`, `email_verification`, `register_role` |
| Perfil | `profile.enabled` |
| Suplantación | `impersonation.enabled`, `permission`, `banner`, `exclude_ids` |
| Layout | `layout.mode` (sidebar\|topbar\|dual), `sidebar_position`, `density`, `content_width`, `sidebar_collapsible`, `table_striped`, `table_compact`, `global_search`, `per_page_options`, `footer_links` |
| Marca | `brand.name`, `logo`, `logo_height`, `favicon`, `tagline` |
| Tema | `theme.preset`, `presets_file`, `default` (dark\|light), `font`, `radius`, `sidebar_width`, `colors.*`, `light.*`, `dark.*` |
| Auth UI | `auth_ui.layout` (centered\|split), `background`, `image`, `show_tagline` |
| Custom | `customization.css`, `customization.head_view`, `slots.*` |
| Import | `import.enabled`, `preview`, `upsert`, `upsert_key` |
| Permisos | `permissions.enabled`, `driver`, `panel_access`, `resources`, `manage_permission` |
| Navegación | `navigation`, `navigation_groups_expanded`, `discovery`, `pages.discovery` |
| Extensiones | `extensions.field_views`, `column_views`, `widgets`, `slots` |
| Otros | `widgets`, `forms_in_modal`, `per_page`, `policies`, `integrations.alertas` |

---

## Layout — valores posibles

| Clave | Valores |
|-------|---------|
| `layout.mode` | `sidebar`, `topbar`, `dual` |
| `layout.sidebar_position` | `left`, `right` |
| `layout.density` | `comfortable`, `compact` |
| `layout.content_width` | `full`, `boxed` |
| `layout.sidebar_collapsed_width` | CSS (ej. `4.5rem`) |
| `layout.per_page_options` | array de enteros |

---

## Tema — presets incluidos

`minimal`, `corporate`, `contrast`, `ocean` (+ los de `theme.presets_file`).

Colores en `theme.colors`: `primary`, `primary_hover`, `primary_dark`, `primary_hover_dark`, `accent`, `accent_dark`, `success`, `danger`, `warning`.

Tokens por modo en `theme.light` / `theme.dark`: `bg`, `surface`, `card`, `elevated`, `border`, `heading`, `text`, `muted`, `input_bg`, `input_border`.

---

## Slots Blade

| Slot | Ubicación |
|------|-----------|
| `sidebar.before` / `sidebar.after` | Menú lateral |
| `main.before` / `main.after` | Contenido principal |
| `topbar.end` | Barra superior (acciones) |

```php
'slots' => ['main.before' => 'mi-app.panel.aviso'],
// o PanelExtensions::registrarSlot('main.before', 'mi-app.panel.aviso');
```

---

## Resource — hooks PHP

| Método | Retorno | Uso |
|--------|---------|-----|
| `form()` | array Fields | Crear/editar |
| `table()` | array Columns | Listado |
| `detail()` | array | Vista ver |
| `import()` | array | Columnas import |
| `filters()` | array | Filtros |
| `relations()` | array RelationManager | Pestañas en ver |
| `navigationBadge()` | ?string | Badge menú |
| `hiddenFromNavigation()` | bool | Ocultar del menú |
| `importUpsertKey()` | ?string | Clave upsert |
| `perPageOptions()` | array | Paginación |
| `canViewAny()` … | bool | Autorización |
| `policy()` | ?string | Policy Laravel |

---

## Page custom

```php
final class MiPage extends Page
{
    protected static ?string $slug = 'mi-pagina';
    protected static ?string $permission = 'ver informes';

    public static function view(): string
    {
        return 'panel.pages.mi-pagina';
    }
}
```

Ruta: `{path}/pages/{slug}`.

---

## Fields incluidos

`TextField`, `EmailField`, `PasswordField`, `TextareaField`, `BooleanField`, `SelectField`, `BelongsToField`, `NumberField`, `DateField`, `DateTimeField`, `ColorField`, `KeyValueField`, `CustomField`, `FileField`, `ImageField`, `RichTextField`, `RolesField`, `PermissionsField`, `RepeaterField`.

## Columns

`TextColumn`, `BooleanColumn`, `DateTimeColumn`, `BadgeColumn`, `ColorColumn`, `BelongsToColumn`, `ImageColumn`, `RolesColumn`, `PermissionsColumn`.

## Filtros

`SelectFilter`, `BooleanFilter`, `DateRangeFilter`, `MultiSelectFilter`.

---

## Widgets

```php
ResourceCountWidget::make(ProductResource::class),
StatWidget::make('Total', fn () => Model::count())->icon('box'),
ChartWidget::make('Ventas', 'bar', fn () => ['labels'=>[], 'values'=>[]])->themeColors(),
ViewWidget::make('Custom', 'panel.widgets.mi-vista', fn () => ['total' => 1]),
```

Tipos gráfico: `bar`, `line`, `pie`, `doughnut`, `progression`.

---

## Comandos

| Comando | Descripción |
|---------|-------------|
| `panel:install` | Instalar (`--demo` stubs) |
| `panel:make-resource` | CRUD Resource |
| `panel:make-page` | Page custom |
| `panel:make-policy` | Policy |
| `panel:upgrade-views` | Actualizar vistas publicadas |
| `vendor:publish --tag=panel-config` | Config |
| `vendor:publish --tag=panel-views` | Vistas |
| `vendor:publish --tag=panel-documentation` | Esta documentación |

---

## Desactivar documentación en producción

```php
'documentation' => ['enabled' => false],
```

---

Fuente de verdad del catálogo interactivo: `src/Support/PanelDocumentacion.php`.
