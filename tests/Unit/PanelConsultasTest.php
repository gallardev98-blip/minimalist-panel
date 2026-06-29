<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Columns\BelongsToColumn;
use MyLaravelTools\Panel\Columns\TextColumn;
use MyLaravelTools\Panel\Support\PanelConsultas;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

final class PanelConsultasTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_fusiona_relaciones_de_columnas(): void
    {
        $columnas = [
            TextColumn::make('title'),
            BelongsToColumn::make('category_id')->relationship('category', 'name'),
        ];

        $this->assertEqualsCanonicalizing(
            ['category', 'author'],
            PanelConsultas::relacionesDesdeTabla($columnas, ['author']),
        );
    }

    public function test_opciones_relacion_usa_cache(): void
    {
        Cache::flush();
        config(['panel.performance.filter_options_cache' => true]);

        Article::query()->create(['title' => 'Cached', 'published' => true]);

        $primera = PanelConsultas::opcionesRelacion(Article::class, 'title');
        Article::query()->create(['title' => 'Nueva', 'published' => true]);
        $segunda = PanelConsultas::opcionesRelacion(Article::class, 'title');

        $this->assertSame($primera, $segunda);
        $this->assertArrayHasKey(1, $primera);
    }

    public function test_eager_load_desactivado_respeta_solo_manual(): void
    {
        config(['panel.performance.eager_load_columns' => false]);

        $columnas = [
            BelongsToColumn::make('category_id')->relationship('category', 'name'),
        ];

        $this->assertSame(['manual'], PanelConsultas::eagerLoadsParaIndice($columnas, ['manual']));
    }

    public function test_eager_loads_for_index_en_recurso(): void
    {
        $this->assertSame([], ArticleResource::eagerLoadsForIndex());
    }
}
