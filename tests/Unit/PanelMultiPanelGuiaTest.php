<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelMultiPanelGuia;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelMultiPanelGuiaTest extends TestCase
{
    public function test_codigo_incluye_panels_y_panel_route(): void
    {
        $codigo = PanelMultiPanelGuia::codigo();

        $this->assertStringContainsString("'panels'", $codigo);
        $this->assertStringContainsString('panel_route', $codigo);
        $this->assertStringContainsString('--multi', $codigo);
    }

    public function test_paneles_demo_tienen_dos_entradas(): void
    {
        $paneles = PanelMultiPanelGuia::panelesDemo();

        $this->assertCount(2, $paneles);
        $this->assertSame('admin', $paneles[0]['id']);
        $this->assertSame('cliente', $paneles[1]['id']);
    }
}
