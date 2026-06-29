<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelRendimiento;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelRendimientoTest extends TestCase
{
    public function test_valores_por_defecto_mas_rapidos(): void
    {
        config()->set('panel.performance', []);

        $this->assertSame(200, PanelRendimiento::debounceBusquedaMs());
        $this->assertSame(50, PanelRendimiento::retardoSkeletonMs());
        $this->assertSame(120, PanelRendimiento::spaLoaderMinMs());
        $this->assertTrue(PanelRendimiento::eagerLoadColumnas());
        $this->assertFalse(PanelRendimiento::paginacionCursor());
    }

    public function test_respeta_configuracion_personalizada(): void
    {
        config()->set('panel.performance', [
            'search_debounce_ms' => 400,
            'skeleton_delay_ms' => 120,
            'select_close_ms' => 180,
        ]);

        $this->assertSame(400, PanelRendimiento::debounceBusquedaMs());
        $this->assertSame(120, PanelRendimiento::retardoSkeletonMs());
        $this->assertSame(180, PanelRendimiento::cierreSelectMs());
    }

    public function test_limita_valores_extremos(): void
    {
        config()->set('panel.performance', [
            'search_debounce_ms' => 99999,
            'skeleton_delay_ms' => -10,
        ]);

        $this->assertSame(1000, PanelRendimiento::debounceBusquedaMs());
        $this->assertSame(0, PanelRendimiento::retardoSkeletonMs());
    }
}
