<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelDocumentacion;
use MyLaravelTools\Panel\Support\PanelPlayground;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelPlaygroundTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        PanelPlayground::reiniciar();
    }

    public function test_guarda_y_aplica_sobreescrituras(): void
    {
        PanelPlayground::guardar('layout.mode', 'dual');
        PanelPlayground::aplicarDesdeSesion();

        $this->assertSame('dual', config('panel.layout.mode'));
    }

    public function test_reiniciar_borra_sesion(): void
    {
        PanelPlayground::guardar('brand.name', 'Prueba');
        PanelPlayground::reiniciar();

        $this->assertFalse(PanelPlayground::tieneSobreescrituras());
    }

    public function test_exportar_genera_fragmento_php(): void
    {
        PanelPlayground::guardar('layout.mode', 'dual');
        PanelPlayground::guardar('brand.name', 'Mi Panel');

        $fragmento = PanelPlayground::exportarFragmento();

        $this->assertStringContainsString("'layout' =>", $fragmento);
        $this->assertStringContainsString("'mode' => 'dual'", $fragmento);
        $this->assertStringContainsString("'name' => 'Mi Panel'", $fragmento);
    }

    public function test_listar_cambios_con_etiquetas(): void
    {
        PanelPlayground::guardar('brand.name', 'Test');

        $cambios = PanelPlayground::listarCambios();

        $this->assertCount(1, $cambios);
        $this->assertSame('brand.name', $cambios[0]['clave']);
        $this->assertSame('Nombre', $cambios[0]['etiqueta']);
    }

    public function test_documentacion_tiene_secciones(): void
    {
        $secciones = PanelDocumentacion::secciones();

        $this->assertNotEmpty($secciones);
        $this->assertNotEmpty(PanelDocumentacion::clavesInteractivas());
        $this->assertNotEmpty(PanelDocumentacion::gruposUsuario());
    }
}
