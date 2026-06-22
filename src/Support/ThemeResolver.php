<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class ThemeResolver
{
    /** @return array<string, string> */
    public static function lightVariables(): array
    {
        $theme = ThemePresets::resolver();
        $colors = $theme['colors'] ?? [];
        $light = $theme['light'] ?? [];

        $primary = (string) ($colors['primary'] ?? '#000000');
        $primaryHover = (string) ($colors['primary_hover'] ?? '#262626');
        $accent = (string) ($colors['accent'] ?? '#525252');

        return [
            'panel-primary' => self::toRgb($primary),
            'panel-primary-hover' => self::toRgb($primaryHover),
            'panel-primary-fg' => self::toRgb(self::contrastFor($primary)),
            'panel-accent' => self::toRgb($accent),
            'panel-ring' => self::toRgb($primary),
            'panel-bg' => self::toRgb((string) ($light['bg'] ?? '#ffffff')),
            'panel-surface' => self::toRgb((string) ($light['surface'] ?? '#fafafa')),
            'panel-card' => self::toRgb((string) ($light['card'] ?? '#ffffff')),
            'panel-elevated' => self::toRgb((string) ($light['elevated'] ?? '#f5f5f5')),
            'panel-border' => self::toRgb((string) ($light['border'] ?? '#e5e5e5')),
            'panel-heading' => self::toRgb((string) ($light['heading'] ?? '#0a0a0a')),
            'panel-text' => self::toRgb((string) ($light['text'] ?? '#404040')),
            'panel-muted' => self::toRgb((string) ($light['muted'] ?? '#737373')),
            'panel-input-bg' => self::toRgb((string) ($light['input_bg'] ?? '#ffffff')),
            'panel-input-border' => self::toRgb((string) ($light['input_border'] ?? '#d4d4d4')),
            'panel-success' => self::toRgb((string) ($colors['success'] ?? '#16a34a')),
            'panel-danger' => self::toRgb((string) ($colors['danger'] ?? '#dc2626')),
            'panel-warning' => self::toRgb((string) ($colors['warning'] ?? '#ca8a04')),
            'panel-radius' => (string) ($theme['radius'] ?? '0.75rem'),
            'panel-sidebar-width' => (string) ($theme['sidebar_width'] ?? '16rem'),
            'livewire-progress-bar-color' => $primary,
        ];
    }

    /** @return array<string, string> */
    public static function darkVariables(): array
    {
        $theme = ThemePresets::resolver();
        $colors = $theme['colors'] ?? [];
        $dark = $theme['dark'] ?? [];

        $primary = (string) ($colors['primary_dark'] ?? $colors['primary'] ?? '#ffffff');
        $primaryHover = (string) ($colors['primary_hover_dark'] ?? $colors['primary_hover'] ?? '#e5e5e5');
        $accent = (string) ($colors['accent_dark'] ?? $colors['accent'] ?? '#a3a3a3');

        return [
            'panel-primary' => self::toRgb($primary),
            'panel-primary-hover' => self::toRgb($primaryHover),
            'panel-primary-fg' => self::toRgb(self::contrastFor($primary)),
            'panel-accent' => self::toRgb($accent),
            'panel-ring' => self::toRgb($primary),
            'panel-bg' => self::toRgb((string) ($dark['bg'] ?? '#0a0a0a')),
            'panel-surface' => self::toRgb((string) ($dark['surface'] ?? '#111111')),
            'panel-card' => self::toRgb((string) ($dark['card'] ?? '#141414')),
            'panel-elevated' => self::toRgb((string) ($dark['elevated'] ?? '#1a1a1a')),
            'panel-border' => self::toRgb((string) ($dark['border'] ?? '#262626')),
            'panel-heading' => self::toRgb((string) ($dark['heading'] ?? '#fafafa')),
            'panel-text' => self::toRgb((string) ($dark['text'] ?? '#d4d4d4')),
            'panel-muted' => self::toRgb((string) ($dark['muted'] ?? '#737373')),
            'panel-input-bg' => self::toRgb((string) ($dark['input_bg'] ?? '#0a0a0a')),
            'panel-input-border' => self::toRgb((string) ($dark['input_border'] ?? '#404040')),
            'panel-success' => self::toRgb((string) ($colors['success'] ?? '#16a34a')),
            'panel-danger' => self::toRgb((string) ($colors['danger'] ?? '#dc2626')),
            'panel-warning' => self::toRgb((string) ($colors['warning'] ?? '#ca8a04')),
        ];
    }

    public static function fontFamily(): string
    {
        $font = (string) (ThemePresets::resolver()['font'] ?? 'Plus Jakarta Sans');

        return "'{$font}', ui-sans-serif, system-ui, sans-serif";
    }

    public static function googleFontsUrl(): string
    {
        $font = (string) (ThemePresets::resolver()['font'] ?? 'Plus Jakarta Sans');
        $query = str_replace(' ', '+', $font);

        return "https://fonts.googleapis.com/css2?family={$query}:wght@400;500;600;700&display=swap";
    }

    /** @return array<string, string> */
    public static function themeColorMap(): array
    {
        $theme = ThemePresets::resolver();
        $colors = $theme['colors'] ?? [];
        $light = $theme['light'] ?? [];

        return [
            'primary' => (string) ($colors['primary'] ?? '#000000'),
            'accent' => (string) ($colors['accent'] ?? '#525252'),
            'success' => (string) ($colors['success'] ?? '#16a34a'),
            'danger' => (string) ($colors['danger'] ?? '#dc2626'),
            'warning' => (string) ($colors['warning'] ?? '#ca8a04'),
            'muted' => (string) ($light['muted'] ?? '#737373'),
        ];
    }

    /** @param list<string> $keys primary|accent|success|danger|warning|muted */
    public static function chartColors(array $keys): array
    {
        $map = self::themeColorMap();

        return array_map(
            fn (string $key): string => self::toCssRgb((string) ($map[$key] ?? $map['primary'])),
            $keys
        );
    }

    /** @return list<string> */
    public static function defaultChartColorKeys(string $chartType, int $sliceCount): array
    {
        $cycle = ['primary', 'accent', 'success', 'warning', 'danger'];
        $count = max(1, $sliceCount);

        if (in_array($chartType, ['pie', 'doughnut'], true) && $count === 2) {
            return ['success', 'danger'];
        }

        if (in_array($chartType, ['line', 'progression'], true)) {
            return ['primary'];
        }

        $keys = [];
        for ($i = 0; $i < $count; $i++) {
            $keys[] = $cycle[$i % count($cycle)];
        }

        return $keys;
    }

    public static function toCssRgb(string $color): string
    {
        if (str_starts_with(trim($color), 'rgb')) {
            return trim($color);
        }

        return 'rgb('.str_replace(' ', ', ', self::toRgb($color)).')';
    }

    private static function toRgb(string $color): string
    {
        $color = trim($color);

        if ($color === '') {
            return '0 0 0';
        }

        if (str_starts_with($color, 'rgb(')) {
            $channels = preg_replace('/[^0-9,]/', '', $color);
            $parts = array_map('intval', explode(',', (string) $channels));

            return implode(' ', array_slice($parts, 0, 3));
        }

        $hex = ltrim($color, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return '0 0 0';
        }

        return implode(' ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }

    private static function contrastFor(string $color): string
    {
        $rgb = self::toRgb($color);
        [$r, $g, $b] = array_map('intval', explode(' ', $rgb));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.55 ? '#000000' : '#ffffff';
    }
}
