<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Fields\BelongsToField;
use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ImportTemplateExporter
{
    private const SAMPLE_LIMIT = 5;

    /** @param class-string<Resource> $resourceClass */
    public function downloadCsv(string $resourceClass): StreamedResponse
    {
        abort_unless($resourceClass::authorize('create'), 403);

        $fields = ImportColumnHelper::importableFields($resourceClass);
        $filename = $resourceClass::slug() . '-plantilla-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(
            function () use ($resourceClass, $fields): void {
                $handle = fopen('php://output', 'wb');

                if ($handle === false) {
                    return;
                }

                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, $this->headers($fields));

                foreach ($this->sampleRecords($resourceClass) as $record) {
                    fputcsv($handle, $this->row($fields, $record));
                }

                fclose($handle);
            },
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    /** @param class-string<Resource> $resourceClass */
    public function downloadExcel(string $resourceClass): StreamedResponse
    {
        abort_unless($resourceClass::authorize('create'), 403);

        $fields = ImportColumnHelper::importableFields($resourceClass);
        $filename = $resourceClass::slug() . '-plantilla-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(
            function () use ($resourceClass, $fields): void {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                foreach ($this->headers($fields) as $index => $header) {
                    $sheet->setCellValue([$index + 1, 1], $header);
                }

                $row = 2;

                foreach ($this->sampleRecords($resourceClass) as $record) {
                    foreach ($this->row($fields, $record) as $index => $value) {
                        $sheet->setCellValue([$index + 1, $row], $value);
                    }

                    $row++;
                }

                (new Xlsx($spreadsheet))->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    /** @param array<int, Field> $fields */
    /** @return list<string> */
    private function headers(array $fields): array
    {
        return array_map(fn (Field $field): string => $field->getLabel(), $fields);
    }

    /** @param array<int, Field> $fields */
    /** @return list<string> */
    private function row(array $fields, Model $record): array
    {
        return array_map(
            fn (Field $field): string => ImportColumnHelper::formatCellForTemplate($field, $record),
            $fields,
        );
    }

    /** @param class-string<Resource> $resourceClass */
    /** @return iterable<int, Model> */
    private function sampleRecords(string $resourceClass): iterable
    {
        $records = $resourceClass::modelClass()::query()
            ->with($resourceClass::with())
            ->latest('id')
            ->limit(self::SAMPLE_LIMIT)
            ->get();

        if ($records->isEmpty()) {
            return [];
        }

        return $records;
    }
}
