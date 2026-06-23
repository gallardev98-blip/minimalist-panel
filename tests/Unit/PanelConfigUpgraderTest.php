<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelConfigUpgrader;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelConfigUpgraderTest extends TestCase
{
    public function test_fusionar_anade_claves_faltantes(): void
    {
        $host = ['path' => 'mi-admin', 'brand' => ['name' => 'Test']];
        $fusionado = PanelConfigUpgrader::fusionar($host);

        $this->assertSame('mi-admin', $fusionado['path']);
        $this->assertSame('Test', $fusionado['brand']['name']);
        $this->assertArrayHasKey('default', $fusionado);
        $this->assertArrayHasKey('import', $fusionado);
    }

    public function test_claves_anadidas_lista_rutas(): void
    {
        $antes = ['path' => 'admin'];
        $despues = PanelConfigUpgrader::fusionar($antes);
        $anadidas = PanelConfigUpgrader::clavesAnadidas($antes, $despues);

        $this->assertContains('default', $anadidas);
        $this->assertContains('import', $anadidas);
    }
}
