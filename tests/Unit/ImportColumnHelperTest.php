<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Fields\TextField;
use MyLaravelTools\Panel\Support\ImportColumnHelper;
use MyLaravelTools\Panel\Support\PanelLocale;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\Fixtures\ImportCustomSchemaResource;
use MyLaravelTools\Panel\Tests\Fixtures\ImportFormOnlyResource;
use MyLaravelTools\Panel\Tests\TestCase;

final class ImportColumnHelperTest extends TestCase
{
    public function test_importable_fields_excludes_password_and_file_types(): void
    {
        $fields = ImportColumnHelper::importableFields(ArticleResource::class);

        $types = array_map(fn ($f) => $f->getType(), $fields);

        $this->assertContains('text', $types);
        $this->assertNotContains('image', $types);
    }

    public function test_importable_false_excludes_field(): void
    {
        $fields = ImportColumnHelper::importableFields(ImportFormOnlyResource::class);

        $this->assertSame(['title'], array_map(fn ($f) => $f->getName(), $fields));
    }

    public function test_import_schema_overrides_form(): void
    {
        $fields = ImportColumnHelper::importableFields(ImportCustomSchemaResource::class);

        $this->assertCount(1, $fields);
        $this->assertSame('title', $fields[0]->getName());
    }

    public function test_map_headers_matches_label_and_name(): void
    {
        $fields = [
            TextField::make('title')->label('Título'),
            TextField::make('slug')->label('Slug'),
        ];

        $mapped = ImportColumnHelper::mapHeaders($fields, ['Título', 'slug', 'Unknown']);

        $this->assertSame('title', $mapped[0]?->getName());
        $this->assertSame('slug', $mapped[1]?->getName());
        $this->assertNull($mapped[2]);
    }

    public function test_parse_cell_value_boolean(): void
    {
        $field = new class('active') extends Field {
            public function getType(): string
            {
                return 'boolean';
            }
        };

        $this->assertTrue(ImportColumnHelper::parseCellValue($field, '1'));
        $this->assertFalse(ImportColumnHelper::parseCellValue($field, '0'));
    }

    public function test_format_cell_for_template_boolean(): void
    {
        $field = new class('published') extends Field {
            public function getType(): string
            {
                return 'boolean';
            }
        };

        $record = new Article(['published' => true]);

        $this->assertSame('1', ImportColumnHelper::formatCellForTemplate($field, $record));
    }

    public function test_panel_locale_resolve_uses_session(): void
    {
        $this->app['config']->set('panel.locales', ['es' => 'Español', 'en' => 'English']);
        $this->app['config']->set('panel.locale', 'es');

        session(['panel.locale' => 'en']);

        $this->assertSame('en', PanelLocale::resolve());
    }

    public function test_panel_locale_selector_requires_multiple_locales(): void
    {
        $this->app['config']->set('panel.locale_selector', true);
        $this->app['config']->set('panel.locales', ['es' => 'Español']);

        $this->assertFalse(PanelLocale::selectorEnabled());

        $this->app['config']->set('panel.locales', ['es' => 'Español', 'en' => 'English']);

        $this->assertTrue(PanelLocale::selectorEnabled());
    }
}
