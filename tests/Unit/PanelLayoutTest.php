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

    public function test_footer_sin_enlaces_por_defecto(): void
    {
        config()->set('panel.layout.footer_links', []);

        $this->assertSame([], PanelLayout::enlacesFooter());
    }

    public function test_modo_filtros_collapsible_por_defecto(): void
    {
        config()->set('panel.layout.filters', [
            'mode' => 'collapsible',
            'default_open' => true,
            'remember_state' => false,
        ]);

        $this->assertSame('collapsible', PanelLayout::modoFiltros());
        $this->assertTrue(PanelLayout::filtrosAbiertosPorDefecto());
        $this->assertFalse(PanelLayout::recordarEstadoFiltros());
    }

    public function test_modo_filtros_inline(): void
    {
        config()->set('panel.layout.filters.mode', 'inline');

        $this->assertSame('inline', PanelLayout::modoFiltros());
    }

    public function test_opciones_index_y_tabla(): void
    {
        config()->set('panel.layout.table_sticky_header', false);
        config()->set('panel.layout.index', [
            'clickable_rows' => false,
            'mobile_cards' => false,
        ]);

        $this->assertFalse(PanelLayout::tablaCabeceraFija());
        $this->assertFalse(PanelLayout::filasClicables());
        $this->assertFalse(PanelLayout::tarjetasMovil());
        $this->assertStringNotContainsString('panel-table-sticky-header', PanelLayout::clasesTabla());
    }

    public function test_opciones_capa_3(): void
    {
        config()->set('panel.forms.validate_inline', false);
        config()->set('panel.layout.index.bulk_preview', false);
        config()->set('panel.import.guided_summary', false);

        $this->assertFalse(PanelLayout::validacionInlineForm());
        $this->assertFalse(PanelLayout::previewBulk());
        $this->assertFalse(PanelLayout::importacionGuiada());
        $this->assertTrue(PanelLayout::borradorFormulario());
        $this->assertTrue(PanelLayout::focoFormulario());
    }
}
