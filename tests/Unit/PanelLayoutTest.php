<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Fields\RepeaterField;
use MyLaravelTools\Panel\Support\PanelLayout;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelLayoutTest extends TestCase
{
    public function test_densidad_compacta(): void
    {
        config()->set('panel.layout.density', 'compact');

        $this->assertSame('panel-density-compact', PanelLayout::claseDensidad());
        $this->assertSame('1rem', PanelLayout::variablesCss()['panel-density-padding']);
    }

    public function test_contenido_boxed(): void
    {
        config()->set('panel.layout.content_width', 'boxed');

        $this->assertSame('panel-content-boxed', PanelLayout::claseAnchoContenido());
        $this->assertSame('80rem', PanelLayout::variablesCss()['panel-content-max-width']);
    }

    public function test_repeater_field_guarda_json(): void
    {
        $campo = RepeaterField::make('items')
            ->columns(['title' => 'Título', 'qty' => 'Cantidad']);

        $resultado = $campo->dehydrateForStorage([
            ['title' => 'A', 'qty' => '2'],
        ], null);

        $this->assertSame('[{"title":"A","qty":"2"}]', $resultado['value']);
    }

    public function test_auth_split_requiere_imagen(): void
    {
        config()->set('panel.auth_ui', ['layout' => 'split', 'image' => null]);

        $this->assertFalse(PanelLayout::layoutAuthSplit());

        config()->set('panel.auth_ui.image', '/img/auth.jpg');

        $this->assertTrue(PanelLayout::layoutAuthSplit());
    }
}
