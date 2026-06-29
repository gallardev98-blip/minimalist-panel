<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Filters\Filter;
use MyLaravelTools\Panel\Resources\Resource;

final class CriteriosActivosIndex
{
    /**
     * @param  class-string<Resource>  $claseRecurso
     * @return list<array{nombre: string, etiqueta: string, valor: string}>
     */
    public static function chips(string $busqueda, array $valoresFiltro, string $claseRecurso): array
    {
        $chips = [];

        if ($busqueda !== '') {
            $chips[] = [
                'nombre' => 'search',
                'etiqueta' => __('panel::panel.search_chip'),
                'valor' => $busqueda,
            ];
        }

        foreach ($claseRecurso::filters() as $filtro) {
            $nombre = $filtro->getName();
            $valor = $valoresFiltro[$nombre] ?? null;
            $texto = self::textoValorFiltro($filtro, $valor);

            if ($texto === null) {
                continue;
            }

            $chips[] = [
                'nombre' => $nombre,
                'etiqueta' => $filtro->getLabel(),
                'valor' => $texto,
            ];
        }

        return $chips;
    }

    public static function textoValorFiltro(Filter $filtro, mixed $valor): ?string
    {
        if ($filtro->getType() === 'date-range') {
            return self::textoRangoFechas($valor);
        }

        if ($filtro->getType() === 'multi-select') {
            return self::textoMultiSelect($filtro, $valor);
        }

        if ($valor === null || $valor === '') {
            return null;
        }

        $opciones = $filtro->meta()['options'] ?? [];

        if (is_array($opciones) && array_key_exists((string) $valor, $opciones)) {
            return (string) $opciones[(string) $valor];
        }

        return (string) $valor;
    }

    private static function textoRangoFechas(mixed $valor): ?string
    {
        if (! is_array($valor)) {
            return null;
        }

        $desde = trim((string) ($valor['from'] ?? ''));
        $hasta = trim((string) ($valor['to'] ?? ''));

        if ($desde === '' && $hasta === '') {
            return null;
        }

        $partes = [];

        if ($desde !== '') {
            $partes[] = __('panel::panel.filter_from').' '.$desde;
        }

        if ($hasta !== '') {
            $partes[] = __('panel::panel.filter_to').' '.$hasta;
        }

        return implode(' · ', $partes);
    }

    /** @param  array<string|int, string>  $opciones */
    private static function textoMultiSelect(Filter $filtro, mixed $valor): ?string
    {
        if (! is_array($valor) || $valor === []) {
            return null;
        }

        $opciones = $filtro->meta()['options'] ?? [];
        $etiquetas = [];

        foreach ($valor as $clave) {
            $claveTexto = (string) $clave;
            $etiquetas[] = $opciones[$clave] ?? $opciones[$claveTexto] ?? $claveTexto;
        }

        return implode(', ', $etiquetas);
    }
}
