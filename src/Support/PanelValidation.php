<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelValidation
{
    /** @return array<string, list<string>> */
    public static function loginCredentials(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /** @return array<string, string> */
    public static function loginMessages(): array
    {
        return [
            'email.required' => __('panel::panel.validation.required', [
                'attribute' => __('panel::panel.auth.email'),
            ]),
            'email.email' => __('panel::panel.validation.email', [
                'attribute' => __('panel::panel.auth.email'),
            ]),
            'password.required' => __('panel::panel.validation.required', [
                'attribute' => __('panel::panel.auth.password'),
            ]),
        ];
    }
}
