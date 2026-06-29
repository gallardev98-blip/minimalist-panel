<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelListado;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;

final class PanelListadoTest extends TestCase
{
    public function test_texto_rango_con_registros(): void
    {
        $paginador = new LengthAwarePaginator(
            items: collect([1, 2, 3]),
            total: 12,
            perPage: 3,
            currentPage: 1,
        );

        $this->assertSame(
            __('panel::panel.results_range', ['from' => 1, 'to' => 3, 'total' => 12]),
            PanelListado::textoRango($paginador),
        );
    }

    public function test_texto_rango_sin_resultados(): void
    {
        $paginador = new LengthAwarePaginator([], 0, 15, 1);

        $this->assertSame(__('panel::panel.results_none'), PanelListado::textoRango($paginador));
    }
}
