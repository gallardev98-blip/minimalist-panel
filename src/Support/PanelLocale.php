<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelLocale
{
    public static function apply(): void
    {
        $locale = self::resolve();

        if ($locale !== null) {
            app()->setLocale($locale);
        }
    }

    public static function resolve(): ?string
    {
        $session = session('panel.locale');

        if (is_string($session) && $session !== '' && self::isAllowed($session)) {
            return $session;
        }

        $configured = config('panel.locale');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return null;
    }

    /** @return array<string, string> */
    public static function available(): array
    {
        $locales = config('panel.locales', []);

        return is_array($locales) ? $locales : [];
    }

    public static function selectorEnabled(): bool
    {
        if (! (bool) config('panel.locale_selector', true)) {
            return false;
        }

        return count(self::available()) > 1;
    }

    public static function isAllowed(string $locale): bool
    {
        return array_key_exists($locale, self::available());
    }

    public static function set(string $locale): void
    {
        if (! self::isAllowed($locale)) {
            return;
        }

        session(['panel.locale' => $locale]);
    }
}
