<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelRutas;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\Route;

final class PanelRouteTest extends TestCase
{
    public function test_helper_global_genera_ruta_login(): void
    {
        $this->assertTrue(function_exists('panel_route'));
        $this->assertTrue(Route::has('panel.login'));
        $this->assertStringContainsString('login', \panel_route('login', [], false));
    }

    public function test_panel_rutas_desde_clase(): void
    {
        $this->assertSame('panel.dashboard', PanelRutas::nombre('dashboard'));
        $this->assertStringContainsString('admin', PanelRutas::url('dashboard', [], false));
    }
}
