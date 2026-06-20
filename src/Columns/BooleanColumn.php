<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

use Illuminate\Database\Eloquent\Model;

final class BooleanColumn extends Column
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function resolve(Model $record): mixed
    {
        return (bool) parent::resolve($record);
    }
}
