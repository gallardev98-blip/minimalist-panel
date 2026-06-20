<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\ExportColumnHelper;
use MyLaravelTools\Panel\Support\GlobalSearch;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class GlobalSearchTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app['config']->set('panel.resources', [ArticleResource::class]);
    }

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

    public function test_it_finds_navigation_and_records(): void
    {
        Article::query()->create(['title' => 'Unique Product Name', 'published' => true]);

        $results = app(GlobalSearch::class)->search('Unique');

        $this->assertNotEmpty($results);
        $this->assertTrue(collect($results)->contains(fn (array $item): bool => $item['type'] === 'record'));
    }
}
