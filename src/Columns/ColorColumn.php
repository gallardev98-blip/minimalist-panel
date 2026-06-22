<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

use Illuminate\Database\Eloquent\Model;

final class ColorColumn extends Column
{
    public function getType(): string
    {
        return 'color';
    }

    public function resolve(Model $record): mixed
    {
        $valor = parent::resolve($record);

        if (! is_string($valor) || $valor === '') {
            return null;
        }

        return str_starts_with($valor, '#') ? $valor : '#'.$valor;
    }
}
