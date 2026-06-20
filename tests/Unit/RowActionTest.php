<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Actions\RowAction;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class RowActionTest extends TestCase
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

    public function test_default_row_actions_include_view_and_edit(): void
    {
        $article = Article::query()->create(['title' => 'Test Article', 'published' => true]);

        $actions = ArticleResource::rowActions();
        $names = array_map(fn (RowAction $action): string => $action->getName(), $actions);

        $this->assertContains('view', $names);
        $this->assertContains('edit', $names);

        $view = collect($actions)->first(fn (RowAction $a): bool => $a->getName() === 'view');
        $this->assertNotNull($view);
        $this->assertTrue($view->isVisible($article, ArticleResource::class));
        $this->assertStringContainsString('articles', $view->resolveUrl($article, 'articles') ?? '');
    }

    public function test_record_title_uses_searchable_column(): void
    {
        $article = Article::query()->create(['title' => 'My Title', 'published' => true]);

        $this->assertSame('My Title', ArticleResource::recordTitle($article));
    }
}
