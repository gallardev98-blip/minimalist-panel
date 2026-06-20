<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Resources\Spatie;

use MyLaravelTools\Panel\Columns\PermissionsColumn;
use MyLaravelTools\Panel\Columns\TextColumn;
use MyLaravelTools\Panel\Fields\PermissionsField;
use MyLaravelTools\Panel\Fields\TextField;
use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\PanelPermission;
use Illuminate\Database\Eloquent\Model;

final class RoleResource extends Resource
{
    /** @var class-string<Model> */
    protected static string $model = 'Spatie\Permission\Models\Role';

    protected static ?string $label = null;

    protected static ?string $icon = 'shield';

    protected static ?string $slug = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    /** @return array<int, string> */
    public static function with(): array
    {
        return ['permissions'];
    }

    public static function label(): string
    {
        return __('panel::panel.permissions.roles');
    }

    public static function canViewAny(): bool
    {
        return static::canManageAccess();
    }

    public static function canView(Model $record): bool
    {
        return static::canManageAccess();
    }

    public static function canCreate(): bool
    {
        return static::canManageAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canManageAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canManageAccess();
    }

    /** @return array<int, \MyLaravelTools\Panel\Fields\Field> */
    public static function form(): array
    {
        return [
            TextField::make('name')
                ->label(__('panel::panel.permissions.name'))
                ->required(),
            TextField::make('guard_name')
                ->label(__('panel::panel.permissions.guard'))
                ->default((string) config('panel.guard', 'web'))
                ->required(),
            PermissionsField::make('permissions')
                ->label(__('panel::panel.permissions.permissions')),
        ];
    }

    /** @return array<int, \MyLaravelTools\Panel\Columns\Column> */
    public static function table(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('panel::panel.permissions.name'))
                ->searchable()
                ->sortable(),
            PermissionsColumn::make('permissions')
                ->label(__('panel::panel.permissions.permissions')),
            TextColumn::make('guard_name')
                ->label(__('panel::panel.permissions.guard')),
        ];
    }

    private static function canManageAccess(): bool
    {
        return PanelPermission::check(PanelPermission::manageAccessPermission());
    }
}
