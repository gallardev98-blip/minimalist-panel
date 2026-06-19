<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

final class SpatieRoles
{
    public static function available(): bool
    {
        return class_exists(\Spatie\Permission\Models\Role::class);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        if (! static::available()) {
            return [];
        }

        return \Spatie\Permission\Models\Role::query()
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    /** @return array<int, string> */
    public static function roleNames(): array
    {
        return array_keys(static::options());
    }
}
