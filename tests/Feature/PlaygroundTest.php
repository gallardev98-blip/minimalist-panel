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

    public function test_playground_muestra_shell_layout(): void
    {
        $this->withSession(['panel.playground' => ['layout.mode' => 'dual']])
            ->get('/playground')
            ->assertOk()
            ->assertSee('panel-playground-layout-shell', false);
    }

    public function test_playground_preview_import_en_avanzado(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'import')
            ->assertSee(__('panel::panel.documentation.import_preview_title'), false);
    }

    public function test_playground_preview_permisos_reacciona(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->set('valores.permissions.enabled', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'permissions')
            ->assertSee(__('panel::panel.documentation.perm_preview_title'), false)
            ->assertSee('manage users', false);
    }

    public function test_playground_guia_extensiones(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'extensions')
            ->assertSee(__('panel::panel.documentation.ext_guide_title'), false)
            ->assertSee('CustomField::make', false);
    }

    public function test_playground_guia_saas(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'extensions')
            ->assertSee(__('panel::panel.documentation.saas_guide_title'), false)
            ->assertSee('panel:install --saas', false);
    }

    public function test_playground_guia_relaciones(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'resources')
            ->assertSee(__('panel::panel.documentation.rel_guide_title'), false)
            ->assertSee('RelationManager::hasOne', false);
    }

    public function test_playground_guia_multi_panel(): void
    {
        Livewire::test(PlaygroundApp::class)
            ->set('mostrarControles', true)
            ->call('seleccionarSeccion', 'mas')
            ->call('seleccionarSeccionTecnica', 'multi_panel')
            ->assertSee(__('panel::panel.documentation.multi_guide_title'), false)
            ->assertSee('panel:install --multi', false);
    }
}
