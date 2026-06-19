<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Resources\Resource;
use Panel\Minimalist\Resources\Spatie\PermissionResource;
use Panel\Minimalist\Resources\Spatie\RoleResource;

final class SpatieResourceRegistrar
{
    /** @return array<int, class-string<Resource>> */
    public static function resources(): array
    {
        if (! static::shouldRegister()) {
            return [];
        }

        return [
            RoleResource::class,
            PermissionResource::class,
        ];
    }

    public static function shouldRegister(): bool
    {
        if (! PanelPermission::enabled()) {
            return false;
        }

        if (! (bool) config('panel.permissions.resources', true)) {
            return false;
        }

        return SpatieRoles::available() && SpatiePermissions::available();
    }
}
