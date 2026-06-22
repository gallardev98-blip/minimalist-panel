<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;

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
}
