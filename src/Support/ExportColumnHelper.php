<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Resources\Resource;

final class ExportColumnHelper
{
    /** @param class-string<Resource> $resourceClass */
    /** @return array<int, Column> */
    public static function exportableColumns(string $resourceClass): array
    {
        return array_values(array_filter(
            $resourceClass::table(),
            fn (Column $column): bool => ! in_array($column->getType(), ['image'], true),
        ));
    }

    public static function formatCellValue(mixed $value): string
    {
        if (is_array($value)) {
            return (string) ($value['value'] ?? json_encode($value, JSON_UNESCAPED_UNICODE));
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) ($value ?? '');
    }
}
