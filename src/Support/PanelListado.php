<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use Illuminate\Contracts\Pagination\CursorPaginator;

final class PanelListado
{
    public static function textoRango(mixed $paginador): string
    {
        if ($paginador instanceof CursorPaginator) {
            $cantidad = $paginador->count();

            if ($cantidad === 0) {
                return (string) __('panel::panel.results_none');
            }

            return (string) __('panel::panel.results_cursor', ['count' => $cantidad]);
        }

        $total = (int) $paginador->total();

        if ($total === 0) {
            return (string) __('panel::panel.results_none');
        }

        if ($paginador->count() === 0) {
            return (string) __('panel::panel.results_total', ['count' => $total]);
        }

        return (string) __('panel::panel.results_range', [
            'from' => $paginador->firstItem(),
            'to' => $paginador->lastItem(),
            'total' => $total,
        ]);
    }

    public static function objetivosCarga(): string
    {
        return 'search,filterValues,resetFilters,quitarCriterio,trashed,sortBy,gotoPage,nextPage,previousPage,perPage';
    }

    public static function objetivosCargaRelacion(): string
    {
        return 'gotoPage,nextPage,previousPage';
    }
}
