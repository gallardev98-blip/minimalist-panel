<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class PanelAuth
{
    public static function enabled(): bool
    {
        return (bool) config('panel.auth.enabled', true);
    }

    public static function registrationEnabled(): bool
    {
        return static::enabled() && (bool) config('panel.auth.register', true);
    }

    public static function passwordResetEnabled(): bool
    {
        return static::enabled() && (bool) config('panel.auth.password_reset', true);
    }

    /** @return class-string<Model&Authenticatable> */
    public static function userModel(): string
    {
        $configured = config('panel.auth.user_model');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return (string) config('auth.providers.users.model', 'App\\Models\\User');
    }

    public static function loginRouteName(): string
    {
        if (static::enabled()) {
            return 'panel.login';
        }

        return (string) config('panel.login_route', 'login');
    }

    public static function logoutRouteName(): string
    {
        if (static::enabled()) {
            return 'panel.logout';
        }

        return (string) config('panel.logout_route', 'logout');
    }

    public static function redirectAfterLogin(): string
    {
        $route = config('panel.auth.redirect_after_login');

        if (is_string($route) && $route !== '') {
            return $route;
        }

        return 'panel.dashboard';
    }

    public static function redirectAfterRegister(): string
    {
        $route = config('panel.auth.redirect_after_register');

        if (is_string($route) && $route !== '') {
            return $route;
        }

        return static::redirectAfterLogin();
    }

    public static function guard(): string
    {
        return (string) config('panel.guard', 'web');
    }

    public static function login(Authenticatable $user, bool $remember = false): void
    {
        Auth::guard(static::guard())->login($user, $remember);
        session()->regenerate();
    }

    public static function assignRegisteredRole(Authenticatable $user): void
    {
        $role = config('panel.auth.register_role');

        if (! is_string($role) || $role === '') {
            return;
        }

        if (! method_exists($user, 'assignRole')) {
            return;
        }

        $user->assignRole($role);
    }
}
