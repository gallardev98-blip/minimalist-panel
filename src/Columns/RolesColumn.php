<?php

declare(strict_types=1);

namespace Panel\Minimalist\Columns;

use Illuminate\Database\Eloquent\Model;

final class RolesColumn extends Column
{
    public function getType(): string
    {
        return 'roles';
    }

    public function resolve(Model $record): mixed
    {
        if (! method_exists($record, 'getRoleNames')) {
            return [];
        }

        return $record->getRoleNames()->values()->all();
    }
}
