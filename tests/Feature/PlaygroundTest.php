<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Livewire\PlaygroundApp;
use MyLaravelTools\Panel\Tests\TestCase;
use Livewire\Livewire;

final class PlaygroundTest extends TestCase
{
    public function test_playground_es_publico_sin_login(): void
    {
        $this->get('/playground')
            ->assertOk()
            ->assertSee(__('panel::panel.documentation.playground_title'), false);
    }

    public function test_playground_aplica_cambios_de_sesion(): void
    {
        $this->withSession(['panel.playground' => ['brand.name' => 'Marca Prueba']])
            ->get('/playground')
            ->assertOk()
            ->assertSee('Marca Prueba', false);
    }

    public function test_playground_aplica_preset_tema_en_estilos(): void
    {
        $this->withSession(['panel.playground' => ['theme.preset' => 'ocean']])
            ->get('/playground')
            ->assertOk()
            ->assertSee('--panel-primary: 14 116 144', false)
            ->assertSee('panel-spa-loader', false);
    }

    public function test_playground_muestra_aviso_widgets(): void
    {
        config()->set('panel.locale', 'es');

        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'graficos')
            ->assertSee('config/panel.php', false)
            ->assertSee('widgets', false);
    }

    public function test_playground_incluye_graficos_montados(): void
    {
        $this->get('/playground')
            ->assertOk()
            ->assertSee('panel-playground-chart-bar', false)
            ->assertSee('data-panel-chart-config', false);
    }

    public function test_playground_livewire_persiste_marca(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('valores.brand.name', 'Marca Livewire');

        $this->assertSame('Marca Livewire', session('panel.playground')['brand.name'] ?? null);
    }

    public function test_playground_livewire_selecciona_tipo_grafico(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->call('seleccionarTipoGrafico', 'line')
            ->assertSet('graficos.tipo_activo', 'line');
    }
}
