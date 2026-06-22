<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class ThemePresets
{
    /** @return list<string> */
    public static function nombres(): array
    {
        return array_keys(self::definiciones());
    }

    /** @return array<string, array<string, mixed>> */
    public static function definiciones(): array
    {
        $paquete = self::cargarArchivo(dirname(__DIR__, 2).'/config/panel-theme-presets.php');
        $personal = self::cargarArchivo(config('panel.theme.presets_file'));

        return array_merge($paquete, $personal);
    }

    /** @return array<string, array<string, mixed>> */
    private static function cargarArchivo(mixed $ruta): array
    {
        if (! is_string($ruta) || $ruta === '' || ! is_file($ruta)) {
            return [];
        }

        $presets = require $ruta;

        return is_array($presets) ? $presets : [];
    }

    /** @return array<string, mixed> */
    public static function resolver(): array
    {
        $config = config('panel.theme', []);
        $preset = $config['preset'] ?? null;

        if (! is_string($preset) || $preset === '') {
            return self::sinPreset($config);
        }

        $base = self::definiciones()[$preset] ?? [];

        if ($base === []) {
            return self::sinPreset($config);
        }

        unset($config['preset']);

        return self::fusionar($base, $config);
    }

    /** @param array<string, mixed> $config */
    private static function sinPreset(array $config): array
    {
        unset($config['preset']);

        return $config;
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private static function fusionar(array $base, array $overrides): array
    {
        foreach ($overrides as $clave => $valor) {
            if (is_array($valor) && isset($base[$clave]) && is_array($base[$clave])) {
                $base[$clave] = self::fusionar($base[$clave], $valor);
                continue;
            }

            $base[$clave] = $valor;
        }

        return $base;
    }
}
