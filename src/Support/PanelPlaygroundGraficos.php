<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Widgets\ChartWidget;

final class PanelPlaygroundGraficos
{
    private const CLAVE_SESION = 'panel.playground.graficos';

    /** @var list<string> */
    public const TIPOS = ['bar', 'line', 'pie', 'doughnut', 'progression'];

    /** @var list<string> */
    public const ESTILOS = ['moderno', 'minimal', 'bold'];

    /** @return array<string, mixed> */
    public static function defecto(): array
    {
        return [
            'tipo_activo' => 'bar',
            'altura' => 200,
            'colores_tema' => true,
            'claves_color' => '',
            'estilo' => 'moderno',
            'borde_radio' => 12,
            'degradado' => true,
            'animacion' => true,
            'leyenda' => true,
            'cutout' => 72,
        ];
    }

    /** @return array<string, mixed> */
    public static function valores(): array
    {
        $guardados = session(self::CLAVE_SESION, []);

        return is_array($guardados)
            ? array_merge(self::defecto(), $guardados)
            : self::defecto();
    }

    public static function guardar(string $clave, mixed $valor): void
    {
        $datos = self::valores();
        $datos[$clave] = $valor;
        session([self::CLAVE_SESION => $datos]);
    }

    public static function reiniciar(): void
    {
        session()->forget(self::CLAVE_SESION);
    }

    public static function tieneCambios(): bool
    {
        $guardados = session(self::CLAVE_SESION, []);

        return is_array($guardados) && $guardados !== [];
    }

    /** @return array{labels: list<string>, values: list<int>} */
    public static function datosDemo(): array
    {
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            'values' => [12, 19, 8, 15, 22],
        ];
    }

    /** @return array<string, mixed> */
    public static function opcionesChart(array $opciones, string $tipo): array
    {
        $estilo = (string) ($opciones['estilo'] ?? 'moderno');
        $preset = self::presetEstilo($estilo);
        $cutout = max(40, min(85, (int) ($opciones['cutout'] ?? $preset['cutout'])));
        $radio = max(0, min(20, (int) ($opciones['borde_radio'] ?? $preset['borde_radio'])));
        $degradado = (bool) ($opciones['degradado'] ?? $preset['degradado']);
        $animacion = (bool) ($opciones['animacion'] ?? true);
        $leyenda = (bool) ($opciones['leyenda'] ?? true);
        $circular = in_array($tipo, ['pie', 'doughnut'], true);

        return [
            'panelStyle' => [
                'preset' => $estilo,
                'borderRadius' => $radio,
                'gradient' => $degradado,
                'animation' => $animacion,
                'legend' => $leyenda,
                'cutout' => $cutout.'%',
                'barPercentage' => $preset['barPercentage'],
                'categoryPercentage' => $preset['categoryPercentage'],
            ],
            'cutout' => $cutout.'%',
            'animation' => $animacion ? ['duration' => 850, 'easing' => 'easeOutQuart'] : false,
            'plugins' => [
                'legend' => ['display' => $leyenda && $circular],
            ],
        ];
    }

    /** @return array{borde_radio: int, degradado: bool, cutout: int, barPercentage: float, categoryPercentage: float} */
    private static function presetEstilo(string $estilo): array
    {
        return match ($estilo) {
            'minimal' => ['borde_radio' => 4, 'degradado' => false, 'cutout' => 65, 'barPercentage' => 0.55, 'categoryPercentage' => 0.7],
            'bold' => ['borde_radio' => 16, 'degradado' => true, 'cutout' => 78, 'barPercentage' => 0.72, 'categoryPercentage' => 0.85],
            default => ['borde_radio' => 12, 'degradado' => true, 'cutout' => 72, 'barPercentage' => 0.62, 'categoryPercentage' => 0.78],
        };
    }

    public static function construir(string $tipo, ?array $opciones = null): ChartWidget
    {
        $opciones ??= self::valores();
        $esActivo = $tipo === (string) ($opciones['tipo_activo'] ?? 'bar');
        $altura = $esActivo ? max(100, (int) ($opciones['altura'] ?? 200)) : 130;
        $optsAplicar = $esActivo ? $opciones : self::defecto();
        $widget = ChartWidget::make(self::etiquetaTipo($tipo), $tipo, self::datosDemo())
            ->height($altura)
            ->options(self::opcionesChart($optsAplicar, $tipo));

        if ($esActivo && ! ($opciones['colores_tema'] ?? true)) {
            $widget->colors(['#6366f1', '#8b5cf6', '#22c55e', '#f59e0b', '#ef4444']);
        } else {
            $claves = self::parsearClaves((string) ($opciones['claves_color'] ?? ''));
            $widget->themeColors($claves !== [] ? $claves : null);
        }

        return $widget;
    }

    /** @return list<ChartWidget> */
    public static function todosLosWidgets(): array
    {
        $opciones = self::valores();

        return array_map(
            fn (string $tipo): ChartWidget => self::construir($tipo, $opciones),
            self::TIPOS,
        );
    }

    public static function exportarCodigo(): string
    {
        $opciones = self::valores();
        $tipo = (string) ($opciones['tipo_activo'] ?? 'bar');
        $altura = max(100, (int) ($opciones['altura'] ?? 200));
        $datos = self::datosDemo();
        $chartOpts = self::opcionesChart($opciones, $tipo);
        unset($chartOpts['panelStyle']);

        $lineas = [
            "ChartWidget::make('".self::etiquetaTipo($tipo)."', '{$tipo}', [",
            "    'labels' => ".self::exportarArray($datos['labels']).',',
            "    'values' => ".self::exportarArray($datos['values']).',',
            '])',
        ];

        if ($opciones['colores_tema'] ?? true) {
            $claves = self::parsearClaves((string) ($opciones['claves_color'] ?? ''));
            $lineas[] = $claves !== []
                ? '    ->themeColors('.self::exportarArray($claves).')'
                : '    ->themeColors()';
        } else {
            $lineas[] = "    ->colors(['#6366f1', '#8b5cf6', '#22c55e', '#f59e0b', '#ef4444'])";
        }

        $lineas[] = '    ->options('.self::exportarOpcionesPhp($chartOpts).')';
        $lineas[] = "    ->height({$altura});";

        return implode("\n", $lineas);
    }

    public static function etiquetaTipo(string $tipo): string
    {
        return (string) __("panel::panel.documentation.chart_types.{$tipo}");
    }

    public static function etiquetaEstilo(string $estilo): string
    {
        return (string) __("panel::panel.documentation.chart_styles.{$estilo}");
    }

    /** @return list<string> */
    private static function parsearClaves(string $texto): array
    {
        if ($texto === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $texto))));
    }

    /** @param list<mixed> $items */
    private static function exportarArray(array $items): string
    {
        $partes = array_map(
            fn (mixed $item): string => is_string($item) ? "'".addslashes($item)."'" : (string) $item,
            $items,
        );

        return '['.implode(', ', $partes).']';
    }

    /** @param array<string, mixed> $opciones */
    private static function exportarOpcionesPhp(array $opciones): string
    {
        return str_replace(['array (', ')'], ['[', ']'], var_export($opciones, true));
    }
}
