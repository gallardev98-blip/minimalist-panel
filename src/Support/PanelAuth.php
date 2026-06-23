<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

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

    public static function emailVerificationEnabled(): bool
    {
        return static::enabled() && (bool) config('panel.auth.email_verification', false);
    }

    public static function requiresEmailVerification(): bool
    {
        if (! static::emailVerificationEnabled()) {
            return false;
        }

        $user = static::user();

        return $user !== null
            && method_exists($user, 'hasVerifiedEmail')
            && ! $user->hasVerifiedEmail();
    }

    public static function profileEnabled(): bool
    {
        return (bool) config('panel.profile.enabled', true);
    }

    public static function profileRouteName(): string
    {
        return PanelRutas::nombre('profile');
    }

    public static function user(): ?Authenticatable
    {
        $user = Auth::guard(static::guard())->user();

        return $user instanceof Authenticatable ? $user : null;
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
            return PanelRutas::nombre('login');
        }

        return (string) config('panel.login_route', 'login');
    }

    public static function logoutRouteName(): string
    {
        if (static::enabled()) {
            return PanelRutas::nombre('logout');
        }

        return (string) config('panel.logout_route', 'logout');
    }

    public static function redirectAfterLogin(): string
    {
        $route = config('panel.auth.redirect_after_login');

        if (is_string($route) && $route !== '') {
            return $route;
        }

        return PanelRutas::nombre('dashboard');
    }

    public static function redirectAfterRegister(): string
    {
        $route = config('panel.auth.redirect_after_register');

        if (is_string($route) && $route !== '') {
            return $route;
        }

        return static::redirectAfterLogin();
    }

    public static function redirectTargetAfterAuth(): string
    {
        $intended = session()->pull('url.intended');
        $loginPath = parse_url(route(static::loginRouteName(), [], false), PHP_URL_PATH) ?: '';
        $default = route(static::redirectAfterLogin(), [], false);

        if (
            is_string($intended)
            && $intended !== ''
            && ($loginPath === '' || ! str_contains($intended, $loginPath))
        ) {
            return $intended;
        }

        return $default;
    }

    public static function redirectTargetAfterRegister(): string
    {
        $intended = session()->pull('url.intended');
        $loginPath = parse_url(route(static::loginRouteName(), [], false), PHP_URL_PATH) ?: '';
        $default = route(static::redirectAfterRegister(), [], false);

        if (
            is_string($intended)
            && $intended !== ''
            && ($loginPath === '' || ! str_contains($intended, $loginPath))
        ) {
            return $intended;
        }

        return $default;
    }

    public static function guard(): string
    {
        return (string) config('panel.guard', 'web');
    }

    public static function login(Authenticatable $user, bool $remember = false): void
    {
        Auth::guard(static::guard())->login($user, $remember);
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
