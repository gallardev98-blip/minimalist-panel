<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelManager;
use MyLaravelTools\Panel\Support\PanelRutas;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        PanelManager::reiniciarDefiniciones();
        parent::tearDown();
    }

    public function test_modo_legacy_un_solo_panel(): void
    {
        $this->assertFalse(PanelManager::multiActivo());
        $this->assertSame('panel.', PanelManager::prefijoRuta());
        $this->assertSame('panel.dashboard', PanelRutas::nombre('dashboard'));
    }

    public function test_multi_panel_activa_prefijos_por_id(): void
    {
        config([
            'panel.default' => 'admin',
            'panel.panels' => [
                'admin' => ['path' => 'admin', 'resources' => []],
                'cliente' => ['path' => 'cliente', 'resources' => []],
            ],
        ]);
        PanelManager::reiniciarDefiniciones();

        $this->assertTrue(PanelManager::multiActivo());
        $this->assertSame('panel.admin.', PanelManager::prefijoRuta('admin'));
        $this->assertSame('panel.cliente.dashboard', PanelRutas::nombre('dashboard', 'cliente'));
    }

    public function test_establecer_contexto_aplica_config_del_panel(): void
    {
        config([
            'panel.panels' => [
                'admin' => ['path' => 'admin', 'brand' => ['name' => 'Admin']],
                'cliente' => ['path' => 'cliente', 'brand' => ['name' => 'Cliente']],
            ],
        ]);
        PanelManager::reiniciarDefiniciones();

        PanelManager::establecerContexto('cliente');

        $this->assertSame('cliente', PanelManager::idActual());
        $this->assertSame('Cliente', config('panel.brand.name'));
    }
}
