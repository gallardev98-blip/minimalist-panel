<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelLocale
{
    public static function apply(): void
    {
        $locale = config('panel.locale');

        if (is_string($locale) && $locale !== '') {
            app()->setLocale($locale);
        }
    }
}
