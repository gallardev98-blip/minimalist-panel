<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelPlaygroundGraficos;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelPlaygroundGraficosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        PanelPlaygroundGraficos::reiniciar();
    }

    public function test_construye_los_cinco_tipos(): void
    {
        $tipos = array_map(
            fn ($widget) => $widget->getChartType(),
            PanelPlaygroundGraficos::todosLosWidgets(),
        );

        $this->assertSame(PanelPlaygroundGraficos::TIPOS, $tipos);
    }

    public function test_exportar_codigo_incluye_opciones(): void
    {
        PanelPlaygroundGraficos::guardar('estilo', 'bold');

        $codigo = PanelPlaygroundGraficos::exportarCodigo();

        $this->assertStringContainsString('->options(', $codigo);
        $this->assertStringContainsString('->height(', $codigo);
    }

    public function test_tiene_cambios_cuando_hay_sesion(): void
    {
        $this->assertFalse(PanelPlaygroundGraficos::tieneCambios());

        PanelPlaygroundGraficos::guardar('altura', 200);

        $this->assertTrue(PanelPlaygroundGraficos::tieneCambios());
    }
}
