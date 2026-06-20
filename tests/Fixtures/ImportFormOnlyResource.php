<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Columns\TextColumn;
use MyLaravelTools\Panel\Fields\TextField;
use MyLaravelTools\Panel\Resources\Resource;

final class ImportFormOnlyResource extends Resource
{
    protected static string $model = Article::class;

    protected static ?string $slug = 'import-form-only';

    public static function form(): array
    {
        return [
            TextField::make('title')->label('Título'),
            TextField::make('slug')->label('Slug')->importable(false),
        ];
    }

    public static function table(): array
    {
        return [
            TextColumn::make('title'),
        ];
    }
}
