<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use Illuminate\Support\Facades\Gate;

final class PanelPermission
{
    public static function enabled(): bool
    {
        return (bool) config('panel.permissions.enabled', false);
    }

    public static function usesSpatie(): bool
    {
        if (config('panel.permissions.driver', 'spatie') !== 'spatie') {
            return false;
        }

        return trait_exists(\Spatie\Permission\Traits\HasRoles::class);
    }

    public static function check(?string $permission): bool
    {
        if ($permission === null || $permission === '') {
            return true;
        }

        if (! static::enabled()) {
            return true;
        }

        $guard = config('panel.guard', 'web');
        $user = auth($guard)->user();

        if ($user === null) {
            return false;
        }

        if (static::usesSpatie() && method_exists($user, 'can')) {
            return $user->can($permission);
        }

        return Gate::forUser($user)->check($permission);
    }

    public static function panelAccessGranted(): bool
    {
        $permission = config('panel.permissions.panel_access');

        if (! is_string($permission) || $permission === '') {
            return true;
        }

        if (! static::enabled()) {
            return true;
        }

        return static::check($permission);
    }

    public static function manageAccessPermission(): string
    {
        return (string) config('panel.permissions.manage_permission', 'manage users');
    }
}
