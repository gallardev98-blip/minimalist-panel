<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PdfExporter;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PdfExporterTest extends TestCase
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

    public function test_it_downloads_pdf_export(): void
    {
        Article::query()->create(['title' => 'PDF Article', 'published' => true]);

        $query = ArticleResource::modelClass()::query();
        $response = app(PdfExporter::class)->download(ArticleResource::class, $query);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }
}
