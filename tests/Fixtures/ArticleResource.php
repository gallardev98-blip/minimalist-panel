<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Columns\BooleanColumn;
use MyLaravelTools\Panel\Columns\TextColumn;
use MyLaravelTools\Panel\Fields\TextField;
use MyLaravelTools\Panel\Filters\BooleanFilter;
use MyLaravelTools\Panel\Resources\Resource;

final class ArticleResource extends Resource
{
    protected static string $model = Article::class;

    protected static ?string $label = 'Articles';

    protected static ?string $slug = 'articles';

    public static function importUpsertKey(): ?string
    {
        return 'title';
    }

    public static function form(): array
    {
        return [
            TextField::make('title')->required(),
        ];
    }

    public static function table(): array
    {
        return [
            TextColumn::make('title')->searchable()->sortable(),
            BooleanColumn::make('published'),
        ];
    }

    public static function filters(): array
    {
        return [
            BooleanFilter::make('published'),
        ];
    }
}
