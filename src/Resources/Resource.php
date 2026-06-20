<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Resources;

use MyLaravelTools\Panel\Actions\BulkAction;
use MyLaravelTools\Panel\Actions\RowAction;
use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Filters\Filter;
use MyLaravelTools\Panel\Relations\RelationManager;
use MyLaravelTools\Panel\Support\FormSchema;
use MyLaravelTools\Panel\Support\ResourceAuthorizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class Resource
{
    /** @var class-string<Model> */
    protected static string $model;

    protected static ?string $label = null;

    /** Nombre de icono Lucide para navegación (ver `resources/views/components/icon.blade.php`). */
    protected static ?string $icon = null;

    protected static ?string $slug = null;

    /** Atributo del modelo para título en breadcrumbs (null = primera columna searchable). */
    protected static ?string $recordTitleAttribute = null;

    /** Policy de Laravel asociada (null = auto-detectar App\\Policies\\{Model}Policy). */
    protected static ?string $policy = null;

    /** @return array<int, Field> */
    abstract public static function form(): array;

    /**
     * Columnas importables (plantilla CSV/Excel e importación).
     * Vacío = usa form() filtrando importable() y tipos no soportados.
     *
     * @return array<int, Field|\MyLaravelTools\Panel\Forms\Section|\MyLaravelTools\Panel\Forms\Tab>
     */
    public static function import(): array
    {
        return [];
    }

    /** @return array<int, Column> */
    abstract public static function table(): array;

    /** @return array<int, Column> */
    public static function detail(): array
    {
        return static::table();
    }

    /** @return array<int, RelationManager> */
    public static function relations(): array
    {
        return [];
    }

    /** @return array<int, Filter> */
    public static function filters(): array
    {
        return [];
    }

    /** @return array<int, BulkAction> */
    public static function bulkActions(): array
    {
        $actions = [
            BulkAction::delete(),
        ];

        if (static::usesSoftDeletes()) {
            $actions[] = BulkAction::restore();
            $actions[] = BulkAction::forceDelete();
        }

        return $actions;
    }

    /** @return array<int, RowAction> */
    public static function rowActions(): array
    {
        $actions = [
            RowAction::view(),
            RowAction::edit(),
            RowAction::delete(),
        ];

        if (static::usesSoftDeletes()) {
            $actions[] = RowAction::restore();
            $actions[] = RowAction::forceDelete();
        }

        return $actions;
    }

    /** @return array<int, string> */
    public static function with(): array
    {
        return [];
    }

    public static function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::modelClass()), true);
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canRestore(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function authorize(string $ability, ?Model $record = null): bool
    {
        return app(ResourceAuthorizer::class)->authorize(static::class, $ability, $record);
    }

    /** @return class-string|null */
    public static function policy(): ?string
    {
        return static::$policy;
    }

    /** @return class-string<Model> */
    public static function modelClass(): string
    {
        if (! isset(static::$model)) {
            throw new InvalidArgumentException(static::class . ' must define protected static string $model');
        }

        return static::$model;
    }

    public static function label(): string
    {
        return static::$label ?? Str::headline(class_basename(static::modelClass()));
    }

    public static function icon(): ?string
    {
        return static::$icon;
    }

    public static function slug(): string
    {
        return static::$slug ?? Str::kebab(class_basename(static::modelClass()));
    }

    /** @return array<string, array<int, string>|string> */
    public static function validationRules(?Model $record = null): array
    {
        $rules = [];

        foreach (FormSchema::fields(static::form()) as $field) {
            $rules[$field->getName()] = $field->getRules($record);
        }

        return $rules;
    }

    /** @return array<string, string> */
    public static function validationMessages(): array
    {
        $messages = [];

        foreach (FormSchema::fields(static::form()) as $field) {
            $messages = array_merge($messages, $field->getValidationMessages());
        }

        return $messages;
    }

    public static function newModel(): Model
    {
        return new (static::modelClass())();
    }

    public static function findRecord(int|string $id, bool $withTrashed = false): Model
    {
        $query = static::modelClass()::query();

        if ($withTrashed && static::usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public static function recordTitle(Model $record): string
    {
        if (static::$recordTitleAttribute !== null) {
            $value = data_get($record, static::$recordTitleAttribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        foreach (static::table() as $column) {
            if (! $column->isSearchable()) {
                continue;
            }

            $value = $column->resolve($record);

            if (is_string($value) && $value !== '') {
                return $value;
            }

            if (is_array($value) && isset($value['value']) && is_string($value['value']) && $value['value'] !== '') {
                return $value['value'];
            }
        }

        return '#' . $record->getKey();
    }
}
