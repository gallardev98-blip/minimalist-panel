<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Illuminate\Database\Eloquent\Model;
use Panel\Minimalist\Support\SpatieRoles;

final class RolesField extends Field
{
    public function getType(): string
    {
        return 'roles';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        if ($record === null || ! SpatieRoles::available()) {
            return [];
        }

        if (! method_exists($record, 'getRoleNames')) {
            return [];
        }

        return $record->getRoleNames()->values()->all();
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
        if (! method_exists($record, 'syncRoles')) {
            return;
        }

        $roles = is_array($value)
            ? array_values(array_filter($value, fn (mixed $role): bool => is_string($role) && $role !== ''))
            : [];

        if (SpatieRoles::available()) {
            $roles = array_values(array_intersect($roles, SpatieRoles::roleNames()));
        }

        $record->syncRoles($roles);
    }

    /** @return array<int, string|\Illuminate\Contracts\Validation\ValidationRule> */
    public function getRules(?Model $record = null): array
    {
        $rules = parent::getRules($record);

        if (! SpatieRoles::available()) {
            return array_merge($rules, ['nullable', 'array']);
        }

        return array_merge($rules, ['nullable', 'array']);
    }

    protected function meta(): array
    {
        return [
            'options' => SpatieRoles::options(),
        ];
    }
}
