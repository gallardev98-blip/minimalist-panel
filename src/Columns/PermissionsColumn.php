<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

use Illuminate\Database\Eloquent\Model;

final class PermissionsColumn extends Column
{
    public function getType(): string
    {
        return 'permissions';
    }

    public function resolve(Model $record): mixed
    {
        if (! method_exists($record, 'getPermissionNames')) {
            return [];
        }

        return $record->getPermissionNames()->values()->all();
    }
}
