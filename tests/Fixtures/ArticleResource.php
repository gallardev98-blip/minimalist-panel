<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Fixtures;

use Panel\Minimalist\Columns\BooleanColumn;
use Panel\Minimalist\Columns\TextColumn;
use Panel\Minimalist\Fields\TextField;
use Panel\Minimalist\Filters\BooleanFilter;
use Panel\Minimalist\Resources\Resource;

final class ArticleResource extends Resource
{
    protected static string $model = Article::class;

    protected static ?string $label = 'Articles';

    protected static ?string $slug = 'articles';

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
