<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Columns\BelongsToColumn;
use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class PanelConsultas
{
    /**
     * @param array<int, Column> $columnas
     * @param array<int, string> $extras
     * @return array<int, string>
     */
    public static function eagerLoadsParaIndice(array $columnas, array $extras = []): array
    {
        if (! PanelRendimiento::eagerLoadColumnas()) {
            return array_values(array_unique($extras));
        }

        return self::relacionesDesdeTabla($columnas, $extras);
    }

    /**
     * @param array<int, Column> $columnas
     * @param array<int, string> $extras
     * @return array<int, string>
     */
    public static function relacionesDesdeTabla(array $columnas, array $extras = []): array
    {
        $relaciones = [];

        foreach ($columnas as $columna) {
            if (! $columna instanceof BelongsToColumn) {
                continue;
            }

            $nombre = $columna->nombreRelacion();

            if ($nombre !== null && $nombre !== '') {
                $relaciones[] = $nombre;
            }
        }

        return array_values(array_unique([...$extras, ...$relaciones]));
    }

    /**
     * @param class-string<Model> $modelo
     * @return array<string|int, string>
     */
    public static function opcionesRelacion(string $modelo, string $columnaTitulo): array
    {
        $resolver = static fn (): array => $modelo::query()
            ->orderBy($columnaTitulo)
            ->pluck($columnaTitulo, 'id')
            ->all();

        if (! PanelRendimiento::cacheOpcionesFiltro()) {
            return $resolver();
        }

        return Cache::remember(
            'panel.opciones.'.md5($modelo.'|'.$columnaTitulo),
            PanelRendimiento::ttlCacheOpciones(),
            $resolver,
        );
    }

    /**
     * @param class-string<Resource> $claseRecurso
     * @return list<array{nivel: string, mensaje: string}>
     */
    public static function auditarRecurso(string $claseRecurso): array
    {
        $resultados = [];
        $tabla = $claseRecurso::table();
        $with = $claseRecurso::with();
        $etiqueta = class_basename($claseRecurso);
        $modelo = $claseRecurso::modelClass();
        $tablaModelo = (new $modelo())->getTable();

        foreach ($tabla as $columna) {
            if (! $columna instanceof BelongsToColumn) {
                continue;
            }

            $relacion = $columna->nombreRelacion();
            $columnaFk = $columna->getName();

            if ($relacion !== null && $relacion !== '' && ! in_array($relacion, $with, true)) {
                $resultados[] = [
                    'nivel' => 'warn',
                    'mensaje' => "{$etiqueta}: BelongsToColumn «{$relacion}» no está en with() — se autocarga si eager_load_columns=true",
                ];
            }

            if (str_ends_with($columnaFk, '_id')) {
                $resultados[] = [
                    'nivel' => 'info',
                    'mensaje' => "{$etiqueta}: índice sugerido en {$tablaModelo}.{$columnaFk}",
                ];
            }
        }

        return $resultados;
    }

    /** @return list<array{nivel: string, mensaje: string}> */
    public static function auditarRecursosRegistrados(): array
    {
        $recursos = config('panel.resources', []);

        if (! is_array($recursos) || $recursos === []) {
            return [['nivel' => 'ok', 'mensaje' => 'No hay resources registrados en config']];
        }

        $resultados = [];

        foreach ($recursos as $clase) {
            if (! is_string($clase) || ! is_subclass_of($clase, Resource::class)) {
                continue;
            }

            $resultados = [...$resultados, ...self::auditarRecurso($clase)];
        }

        if ($resultados === []) {
            return [['nivel' => 'ok', 'mensaje' => 'Resources sin columnas BelongsTo en listados']];
        }

        return $resultados;
    }
}
