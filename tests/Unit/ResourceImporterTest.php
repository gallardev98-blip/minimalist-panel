<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MyLaravelTools\Panel\Support\ResourceImporter;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\Fixtures\ImportCustomSchemaResource;
use MyLaravelTools\Panel\Tests\TestCase;

final class ResourceImporterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->boolean('published')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function test_analyze_path_marks_invalid_rows(): void
    {
        $path = $this->tempCsv("Título\nDemasiado largo\nCorto\n");

        $result = app(ResourceImporter::class)->analyzePath(ImportCustomSchemaResource::class, $path, 'csv');

        $this->assertSame(2, $result['summary']['total']);
        $this->assertSame(1, $result['summary']['valid']);
        $this->assertSame(1, $result['summary']['invalid']);
        $this->assertFalse($result['rows'][0]['valid']);
        $this->assertTrue($result['rows'][1]['valid']);
    }

    public function test_import_payloads_only_persists_valid_data(): void
    {
        $result = app(ResourceImporter::class)->importPayloads(ArticleResource::class, [
            ['title' => 'Importado A'],
            ['title' => 'Importado B'],
        ]);

        $this->assertSame(2, $result['imported']);
        $this->assertSame(0, $result['failed']);
        $this->assertSame(2, Article::query()->whereIn('title', ['Importado A', 'Importado B'])->count());
    }

    private function tempCsv(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'panel-import-');
        file_put_contents($path, $content);

        return $path;
    }
}
