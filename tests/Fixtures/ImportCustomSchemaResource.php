<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Columns\TextColumn;
use MyLaravelTools\Panel\Fields\TextField;
use MyLaravelTools\Panel\Resources\Resource;

final class ImportCustomSchemaResource extends Resource
{
    protected static string $model = Article::class;

    protected static ?string $slug = 'import-custom';

    public static function import(): array
    {
        return [
            TextField::make('title')->label('Título'),
        ];
    }

    public static function form(): array
    {
        return [
            TextField::make('title'),
            TextField::make('slug'),
        ];
    }

    public static function table(): array
    {
        return [
            TextColumn::make('title'),
        ];
    }
}
