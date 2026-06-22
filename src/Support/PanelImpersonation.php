<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class PanelImpersonation
{
    private const CLAVE_SESION = 'panel.impersonator_id';

    public static function enabled(): bool
    {
        return (bool) config('panel.impersonation.enabled', false);
    }

    public static function bannerEnabled(): bool
    {
        return static::enabled() && (bool) config('panel.impersonation.banner', true);
    }

    public static function permission(): string
    {
        return (string) config('panel.impersonation.permission', 'impersonate users');
    }

    /** @return list<int|string> */
    public static function excludedIds(): array
    {
        $ids = config('panel.impersonation.exclude_ids', []);

        return is_array($ids) ? $ids : [];
    }

    public static function isActive(): bool
    {
        return session()->has(static::CLAVE_SESION);
    }

    public static function authorized(): bool
    {
        if (! static::enabled() || PanelAuth::user() === null) {
            return false;
        }

        if (! PanelPermission::enabled()) {
            return true;
        }

        return PanelPermission::check(static::permission());
    }

    public static function impersonator(): ?Authenticatable
    {
        if (! static::isActive()) {
            return null;
        }

        $id = session(static::CLAVE_SESION);
        $model = PanelAuth::userModel();

        return $model::query()->find($id);
    }

    public static function canImpersonate(Model $objetivo): bool
    {
        if (! static::enabled() || static::isActive() || ! static::authorized()) {
            return false;
        }

        $actual = PanelAuth::user();

        if ($actual === null || ! $objetivo instanceof Authenticatable) {
            return false;
        }

        if ($objetivo->getAuthIdentifier() === $actual->getAuthIdentifier()) {
            return false;
        }

        if (in_array($objetivo->getKey(), static::excludedIds(), true)) {
            return false;
        }

        return $objetivo::class === PanelAuth::userModel();
    }

    public static function start(Authenticatable $objetivo): bool
    {
        if (! $objetivo instanceof Model || ! static::canImpersonate($objetivo)) {
            return false;
        }

        $admin = PanelAuth::user();

        if ($admin === null) {
            return false;
        }

        session()->put(static::CLAVE_SESION, $admin->getAuthIdentifier());
        Auth::guard(PanelAuth::guard())->login($objetivo);
        session()->regenerate();

        return true;
    }

    public static function leave(): bool
    {
        if (! static::isActive()) {
            return false;
        }

        $id = session()->pull(static::CLAVE_SESION);
        $model = PanelAuth::userModel();
        $admin = $model::query()->find($id);

        if (! $admin instanceof Authenticatable) {
            Auth::guard(PanelAuth::guard())->logout();

            return false;
        }

        Auth::guard(PanelAuth::guard())->login($admin);
        session()->regenerate();

        return true;
    }
}
