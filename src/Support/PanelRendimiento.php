<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelRendimiento
{
    public static function debounceBusquedaMs(): int
    {
        return self::entero('search_debounce_ms', 200, 0, 1000);
    }

    public static function debounceFechaMs(): int
    {
        return self::entero('filter_date_debounce_ms', 250, 0, 1000);
    }

    public static function debounceMultiSelectMs(): int
    {
        return self::entero('filter_multiselect_debounce_ms', 150, 0, 1000);
    }

    public static function retardoSkeletonMs(): int
    {
        return self::entero('skeleton_delay_ms', 50, 0, 500);
    }

    public static function retardoOcultarTablaMs(): int
    {
        return self::entero('table_hide_delay_ms', 80, 0, 500);
    }

    public static function cierreSelectMs(): int
    {
        return self::entero('select_close_ms', 100, 0, 500);
    }

    public static function spaLoaderMinMs(): int
    {
        return self::entero('spa_loader_min_ms', 120, 0, 2000);
    }

    public static function spaLoaderExitMs(): int
    {
        return self::entero('spa_loader_exit_ms', 160, 0, 2000);
    }

    public static function expandirFiltrosMs(): int
    {
        return self::entero('filter_expand_ms', 220, 0, 1000);
    }

    public static function animacionesReducidas(): bool
    {
        return (bool) config('panel.performance.reduce_animations', false);
    }

    public static function eagerLoadColumnas(): bool
    {
        return (bool) config('panel.performance.eager_load_columns', true);
    }

    public static function cacheOpcionesFiltro(): bool
    {
        return (bool) config('panel.performance.filter_options_cache', true);
    }

    public static function ttlCacheOpciones(): int
    {
        return self::entero('filter_options_cache_ttl', 300, 60, 3600);
    }

    public static function paginacionCursor(): bool
    {
        return (bool) config('panel.performance.cursor_pagination', false);
    }

    /** @return array<string, string> */
    public static function variablesCss(): array
    {
        $rapido = self::animacionesReducidas();

        return [
            'panel-perf-select-close' => self::cierreSelectMs().'ms',
            'panel-perf-filter-expand' => (self::expandirFiltrosMs()).'ms',
            'panel-perf-transition' => $rapido ? '0.01ms' : '0.12s',
            'panel-perf-table-transition' => $rapido ? '0.01ms' : '0.15s',
        ];
    }

    private static function entero(string $clave, int $defecto, int $minimo, int $maximo): int
    {
        $valor = config('panel.performance.'.$clave, $defecto);

        return max($minimo, min($maximo, (int) $valor));
    }
}
