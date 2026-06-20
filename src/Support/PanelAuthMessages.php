<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use Illuminate\Support\Facades\Password;

final class PanelAuthMessages
{
    public static function passwordStatus(string $status): string
    {
        $key = match ($status) {
            Password::RESET_LINK_SENT => 'sent',
            Password::INVALID_USER => 'user',
            Password::INVALID_TOKEN => 'token',
            Password::THROTTLED => 'throttled',
            Password::PASSWORD_RESET => 'reset',
            default => null,
        };

        if ($key === null) {
            return __($status);
        }

        $locale = config('panel.locale');

        return trans(
            'panel::panel.auth.passwords.'.$key,
            [],
            is_string($locale) && $locale !== '' ? $locale : app()->getLocale(),
        );
    }
}
