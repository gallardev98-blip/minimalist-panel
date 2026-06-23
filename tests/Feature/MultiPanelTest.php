<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Support\PanelManager;
use MyLaravelTools\Panel\Support\PanelRutas;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\Route;

final class MultiPanelTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('panel.panels', [
            'admin' => [
                'path' => 'admin',
                'auth' => ['enabled' => true],
                'resources' => [],
            ],
            'cliente' => [
                'path' => 'cliente',
                'auth' => ['enabled' => true],
                'resources' => [],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        PanelManager::reiniciarDefiniciones();
        parent::tearDown();
    }

    public function test_registra_rutas_con_prefijo_por_panel(): void
    {
        $this->assertTrue(PanelManager::multiActivo());
        $this->assertTrue(Route::has(PanelRutas::nombre('dashboard', 'admin')));
        $this->assertTrue(Route::has(PanelRutas::nombre('dashboard', 'cliente')));
        $this->assertTrue(Route::has(PanelRutas::nombre('login', 'cliente')));
    }
}
