<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelManager
{
    private static ?string $actual = null;

    /** @var array<string, array<string, mixed>>|null */
    private static ?array $definiciones = null;

    /** @var array<string, mixed>|null */
    private static ?array $configInicial = null;

    /** @var list<string> */
    private const META = ['default', 'panels', 'documentation'];

    public static function idPorDefecto(): string
    {
        self::asegurarConfigInicial();
        $defecto = self::$configInicial['default'] ?? null;

        return is_string($defecto) && $defecto !== '' ? $defecto : 'admin';
    }

    public static function idActual(): string
    {
        return self::$actual ?? self::idPorDefecto();
    }

    public static function multiActivo(): bool
    {
        return count(self::definiciones()) > 1;
    }

    /** @return list<string> */
    public static function ids(): array
    {
        return array_keys(self::definiciones());
    }

    /** @return array<string, array<string, mixed>> */
    public static function definiciones(): array
    {
        if (self::$definiciones !== null) {
            return self::$definiciones;
        }

        self::asegurarConfigInicial();

        $panels = self::$configInicial['panels'] ?? null;

        if (! is_array($panels) || $panels === []) {
            $id = self::idPorDefecto();
            self::$definiciones = [$id => self::configLegacy()];

            return self::$definiciones;
        }

        $definiciones = [];

        foreach ($panels as $id => $definicion) {
            if (! is_string($id) || $id === '') {
                continue;
            }

            if (is_string($definicion) && is_file($definicion)) {
                $definicion = require $definicion;
            }

            if (! is_array($definicion)) {
                continue;
            }

            $definiciones[$id] = $definicion;
        }

        self::$definiciones = $definiciones !== []
            ? $definiciones
            : [self::idPorDefecto() => self::configLegacy()];

        return self::$definiciones;
    }

    public static function establecerContexto(string $id): void
    {
        $definiciones = self::definiciones();

        if (! isset($definiciones[$id])) {
            $id = array_key_first($definiciones) ?? self::idPorDefecto();
        }

        self::$actual = $id;

        $config = array_replace_recursive($definiciones[$id], self::meta());
        $config['_panel_id'] = $id;

        config(['panel' => $config]);

        app(PanelExtensions::class)->reiniciarDesdeConfig();
        app(PanelSlots::class)->reiniciarDesdeConfig();
        app(ResourceRegistry::class)->limpiarCache();
        app(PageRegistry::class)->limpiarCache();
    }

    public static function prefijoRuta(?string $id = null): string
    {
        $id = $id ?? self::idActual();

        return self::multiActivo() ? "panel.{$id}." : 'panel.';
    }

    public static function reiniciarDefiniciones(): void
    {
        self::$definiciones = null;
        self::$actual = null;
        self::$configInicial = null;
    }

    public static function sincronizarConfigInicial(): void
    {
        self::$configInicial = null;
        self::$definiciones = null;
        self::asegurarConfigInicial();
    }

    /** @return array<string, mixed> */
    private static function meta(): array
    {
        self::asegurarConfigInicial();

        return array_intersect_key(self::$configInicial ?? [], array_flip(self::META));
    }

    /** @return array<string, mixed> */
    private static function configLegacy(): array
    {
        self::asegurarConfigInicial();

        return array_diff_key(self::$configInicial ?? [], array_flip(self::META));
    }

    private static function asegurarConfigInicial(): void
    {
        if (self::$configInicial !== null) {
            return;
        }

        $config = config('panel', []);

        self::$configInicial = is_array($config) ? $config : [];
    }
}
