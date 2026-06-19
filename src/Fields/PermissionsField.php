<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Illuminate\Database\Eloquent\Model;
use Panel\Minimalist\Support\SpatiePermissions;

final class PermissionsField extends Field
{
    public function getType(): string
    {
        return 'permissions';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        if ($record === null || ! SpatiePermissions::available()) {
            return [];
        }

        if (! method_exists($record, 'getPermissionNames')) {
            return [];
        }

        return $record->getPermissionNames()->values()->all();
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        return [
            'value' => is_array($value) ? $value : [],
            'include' => false,
        ];
    }

    public function afterSave(Model $record, mixed $value): void
    {
        if (! method_exists($record, 'syncPermissions')) {
            return;
        }

        $permissions = is_array($value)
            ? array_values(array_filter($value, fn (mixed $permission): bool => is_string($permission) && $permission !== ''))
            : [];

        if (SpatiePermissions::available()) {
            $permissions = array_values(array_intersect($permissions, SpatiePermissions::permissionNames()));
        }

        $record->syncPermissions($permissions);
    }

    /** @return array<int, string|\Illuminate\Contracts\Validation\ValidationRule> */
    public function getRules(?Model $record = null): array
    {
        return array_merge(parent::getRules($record), ['nullable', 'array']);
    }

    protected function meta(): array
    {
        return [
            'options' => SpatiePermissions::options(),
        ];
    }
}
