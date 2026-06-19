<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Fields\TextField;
use Panel\Minimalist\Forms\Section;
use Panel\Minimalist\Forms\Tab;
use Panel\Minimalist\Support\FormSchema;
use Panel\Minimalist\Tests\TestCase;

final class FormSchemaTest extends TestCase
{
    public function test_it_flattens_sections_and_fields(): void
    {
        $schema = [
            Section::make('General', [
                TextField::make('name'),
            ]),
            TextField::make('email'),
        ];

        $fields = FormSchema::fields($schema);

        $this->assertCount(2, $fields);
        $this->assertSame('name', $fields[0]->getName());
        $this->assertSame('email', $fields[1]->getName());
        $this->assertTrue(FormSchema::hasSections($schema));
    }

    public function test_it_flattens_tabs_sections_and_fields(): void
    {
        $schema = [
            Tab::make('General', [
                Section::make('Info', [
                    TextField::make('name'),
                ]),
                TextField::make('email'),
            ]),
            Tab::make('Meta', [
                TextField::make('notes'),
            ]),
        ];

        $fields = FormSchema::fields($schema);

        $this->assertCount(3, $fields);
        $this->assertTrue(FormSchema::hasTabs($schema));
        $this->assertCount(2, FormSchema::tabs($schema));
        $this->assertTrue(FormSchema::hasSections($schema));
    }
}
