<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PlaygroundDemo;
use MyLaravelTools\Panel\Tests\TestCase;

final class PlaygroundDemoTest extends TestCase
{
    public function test_widget_vista_devuelve_view_widget(): void
    {
        $widget = PlaygroundDemo::widgetVista();

        $this->assertSame('panel::widgets.playground-timeline', $widget->getView());
        $this->assertCount(3, $widget->getViewData()['items']);
    }

    public function test_etiqueta_modo_para_topbar(): void
    {
        $this->assertStringContainsString('topbar', strtolower(PlaygroundDemo::etiquetaModo('topbar')));
    }
}
