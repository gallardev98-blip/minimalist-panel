<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\ResourceQuery;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class ResourceQueryTest extends TestCase
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

    public function test_it_filters_by_search_term(): void
    {
        Article::query()->create(['title' => 'Laravel Panel', 'published' => true]);
        Article::query()->create(['title' => 'Other', 'published' => true]);

        $query = (new ResourceQuery(ArticleResource::class))->build(
            columns: ArticleResource::table(),
            filters: ArticleResource::filters(),
            filterValues: ['published' => ''],
            search: 'Laravel',
        );

        $this->assertCount(1, $query->get());
        $this->assertSame('Laravel Panel', $query->first()->title);
    }

    public function test_it_applies_boolean_filter(): void
    {
        Article::query()->create(['title' => 'Published', 'published' => true]);
        Article::query()->create(['title' => 'Draft', 'published' => false]);

        $query = (new ResourceQuery(ArticleResource::class))->build(
            columns: ArticleResource::table(),
            filters: ArticleResource::filters(),
            filterValues: ['published' => '1'],
        );

        $this->assertCount(1, $query->get());
        $this->assertSame('Published', $query->first()->title);
    }

    public function test_it_sorts_by_valid_column(): void
    {
        Article::query()->create(['title' => 'B', 'published' => true]);
        Article::query()->create(['title' => 'A', 'published' => true]);

        $query = (new ResourceQuery(ArticleResource::class))->build(
            columns: ArticleResource::table(),
            filters: ArticleResource::filters(),
            filterValues: ['published' => ''],
            sortColumn: 'title',
            sortDirection: 'asc',
        );

        $this->assertSame(['A', 'B'], $query->pluck('title')->all());
    }
}
