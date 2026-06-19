<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Columns\Column;
use Panel\Minimalist\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExcelExporter
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
        $filename ??= $resourceClass::slug() . '-' . now()->format('Y-m-d-His') . '.xlsx';

        return response()->streamDownload(
            function () use ($query, $columns): void {
                $spreadsheet = $this->buildSpreadsheet($columns, $query);
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    /** @param class-string<Resource> $resourceClass */
    public function downloadSelected(string $resourceClass, iterable $records, ?string $filename = null): StreamedResponse
    {
        $columns = ExportColumnHelper::exportableColumns($resourceClass);
        $filename ??= $resourceClass::slug() . '-seleccion-' . now()->format('Y-m-d-His') . '.xlsx';

        return response()->streamDownload(
            function () use ($records, $columns): void {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $this->writeHeaders($sheet, $columns);

                $row = 2;
                foreach ($records as $record) {
                    $this->writeRow($sheet, $columns, $record, $row);
                    $row++;
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    /**
     * @param array<int, Column> $columns
     * @param Builder<Model> $query
     */
    private function buildSpreadsheet(array $columns, Builder $query): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->writeHeaders($sheet, $columns);

        $row = 2;
        $query->chunk(200, function ($records) use ($sheet, $columns, &$row): void {
            foreach ($records as $record) {
                $this->writeRow($sheet, $columns, $record, $row);
                $row++;
            }
        });

        return $spreadsheet;
    }

    /** @param array<int, Column> $columns */
    private function writeHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $index => $column) {
            $sheet->setCellValue([$index + 1, 1], $column->getLabel());
        }
    }

    /** @param array<int, Column> $columns */
    private function writeRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $columns, Model $record, int $row): void
    {
        foreach ($columns as $index => $column) {
            $sheet->setCellValue(
                [$index + 1, $row],
                ExportColumnHelper::formatCellValue($column->resolve($record)),
            );
        }
    }
}
