<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Filters\BooleanFilter;
use MyLaravelTools\Panel\Filters\DateRangeFilter;
use MyLaravelTools\Panel\Filters\SelectFilter;
use MyLaravelTools\Panel\Support\CriteriosActivosIndex;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;

final class CriteriosActivosIndexTest extends TestCase
{
    public function test_genera_chip_de_busqueda(): void
    {
        $chips = CriteriosActivosIndex::chips('laravel', [], ArticleResource::class);

        $this->assertCount(1, $chips);
        $this->assertSame('search', $chips[0]['nombre']);
        $this->assertSame('laravel', $chips[0]['valor']);
    }

    public function test_genera_chip_de_filtro_booleano(): void
    {
        $chips = CriteriosActivosIndex::chips('', ['published' => '1'], ArticleResource::class);

        $this->assertCount(1, $chips);
        $this->assertSame('published', $chips[0]['nombre']);
        $this->assertSame(__('panel::panel.yes'), $chips[0]['valor']);
    }

    public function test_ignora_filtros_vacios(): void
    {
        $chips = CriteriosActivosIndex::chips('', ['published' => ''], ArticleResource::class);

        $this->assertSame([], $chips);
    }

    public function test_formatea_rango_de_fechas(): void
    {
        $filtro = DateRangeFilter::make('created_at');
        $texto = CriteriosActivosIndex::textoValorFiltro($filtro, [
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]);

        $this->assertStringContainsString('2026-01-01', (string) $texto);
        $this->assertStringContainsString('2026-01-31', (string) $texto);
    }

    public function test_formatea_select_con_opciones(): void
    {
        $filtro = SelectFilter::make('status')->options(['draft' => 'Borrador']);
        $texto = CriteriosActivosIndex::textoValorFiltro($filtro, 'draft');

        $this->assertSame('Borrador', $texto);
    }
}
