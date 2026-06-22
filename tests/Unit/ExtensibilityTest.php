<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelExtensions;
use MyLaravelTools\Panel\Support\ThemePresets;
use MyLaravelTools\Panel\Support\ThemeResolver;
use MyLaravelTools\Panel\Tests\TestCase;

final class ExtensibilityTest extends TestCase
{
    public function test_preset_corporate_cambia_primary(): void
    {
        config()->set('panel.theme', [
            'preset' => 'corporate',
            'colors' => [],
        ]);

        $light = ThemeResolver::lightVariables();

        $this->assertSame('30 64 175', $light['panel-primary']);
    }

    public function test_override_fusiona_sobre_preset(): void
    {
        config()->set('panel.theme', [
            'preset' => 'minimal',
            'colors' => ['primary' => '#ff0000'],
        ]);

        $light = ThemeResolver::lightVariables();

        $this->assertSame('255 0 0', $light['panel-primary']);
    }

    public function test_theme_presets_lista_nombres(): void
    {
        $nombres = ThemePresets::nombres();

        $this->assertContains('minimal', $nombres);
        $this->assertContains('corporate', $nombres);
        $this->assertContains('ocean', $nombres);
    }

    public function test_panel_extensions_registra_vistas(): void
    {
        $extensiones = new PanelExtensions();
        $extensiones->registrarVistaCampo('mi-tipo', 'mi-app.campo');
        $extensiones->registrarVistaColumna('mi-col', 'mi-app.columna');

        $this->assertSame('mi-app.campo', $extensiones->vistaCampo('mi-tipo'));
        $this->assertSame('mi-app.columna', $extensiones->vistaColumna('mi-col'));
        $this->assertNull($extensiones->vistaCampo('otro'));
    }

    public function test_key_value_field_serializa_json(): void
    {
        $campo = \MyLaravelTools\Panel\Fields\KeyValueField::make('meta');
        $resultado = $campo->dehydrateForStorage(['a' => '1', 'b' => '2'], null);

        $this->assertSame('{"a":"1","b":"2"}', $resultado['value']);
    }
}
