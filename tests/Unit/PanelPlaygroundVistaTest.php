<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelPlayground;
use MyLaravelTools\Panel\Support\PanelPlaygroundVista;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelPlaygroundVistaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        PanelPlayground::reiniciar();
    }

    public function test_zona_por_clave_mapea_marca_y_tabla(): void
    {
        $this->assertSame('marca', PanelPlaygroundVista::zonaPorClave('brand.name'));
        $this->assertSame('tabla', PanelPlaygroundVista::zonaPorClave('layout.table_striped'));
        $this->assertSame('menu', PanelPlaygroundVista::zonaPorClave('layout.mode'));
        $this->assertSame('acentos', PanelPlaygroundVista::zonaPorClave('theme.colors.primary'));
        $this->assertSame('tema', PanelPlaygroundVista::zonaPorClave('theme.preset'));
    }

    public function test_zonas_modificadas_sin_duplicados(): void
    {
        PanelPlayground::guardar('brand.name', 'Demo');
        PanelPlayground::guardar('brand.logo', '/logo.svg');

        $zonas = PanelPlaygroundVista::zonasModificadas();

        $this->assertSame(['marca'], $zonas);
    }

    public function test_contar_cambios_seccion(): void
    {
        PanelPlayground::guardar('layout.mode', 'dual');
        PanelPlayground::guardar('layout.density', 'compact');

        $this->assertGreaterThan(0, PanelPlaygroundVista::contarCambiosSeccion('layout'));
    }

    public function test_pista_zona_incluye_etiqueta(): void
    {
        $pista = PanelPlaygroundVista::pistaZona('layout.table_striped');

        $this->assertStringContainsString(__('panel::panel.documentation.zones.tabla'), $pista);
    }
}
