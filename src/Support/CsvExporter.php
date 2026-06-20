<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CsvExporter
{
    /**
     * @param class-string<Resource> $resourceClass
     * @param Builder<Model> $query
     */
    public function download(
        string $resourceClass,
        Builder $query,
        ?string $filename = null,
    ): StreamedResponse {
        $columns = ExportColumnHelper::exportableColumns($resourceClass);
        $filename ??= $resourceClass::slug() . '-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(
            function () use ($query, $columns): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    return;
                }

                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, array_map(fn (Column $column): string => $column->getLabel(), $columns));

                $query->chunk(200, function ($records) use ($handle, $columns): void {
                    foreach ($records as $record) {
                        fputcsv($handle, array_map(
                            fn (Column $column): string => ExportColumnHelper::formatCellValue($column->resolve($record)),
                            $columns,
                        ));
                    }
                });

                fclose($handle);
            },
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    /** @param class-string<Resource> $resourceClass */
    public function downloadSelected(string $resourceClass, iterable $records, ?string $filename = null): StreamedResponse
    {
        $columns = ExportColumnHelper::exportableColumns($resourceClass);
        $filename ??= $resourceClass::slug() . '-seleccion-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(
            function () use ($records, $columns): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    return;
                }

                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, array_map(fn (Column $column): string => $column->getLabel(), $columns));

                foreach ($records as $record) {
                    fputcsv($handle, array_map(
                        fn (Column $column): string => ExportColumnHelper::formatCellValue($column->resolve($record)),
                        $columns,
                    ));
                }

                fclose($handle);
            },
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }
}
