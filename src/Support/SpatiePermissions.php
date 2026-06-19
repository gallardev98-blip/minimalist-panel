<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

final class SpatiePermissions
{
    public static function available(): bool
    {
        return class_exists(\Spatie\Permission\Models\Permission::class);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        if (! static::available()) {
            return [];
        }

        return \Spatie\Permission\Models\Permission::query()
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    /** @return array<int, string> */
    public static function permissionNames(): array
    {
        return array_keys(static::options());
    }
}
