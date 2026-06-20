<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Resources\Resource;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PdfExporter
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
        $filename ??= $resourceClass::slug() . '-' . now()->format('Y-m-d-His') . '.pdf';
        $rows = $this->collectRows($query, $columns);

        return $this->streamPdf(
            $this->renderHtml($resourceClass::label(), $columns, $rows),
            $filename,
        );
    }

    /** @param class-string<Resource> $resourceClass */
    public function downloadSelected(string $resourceClass, iterable $records, ?string $filename = null): StreamedResponse
    {
        $columns = ExportColumnHelper::exportableColumns($resourceClass);
        $filename ??= $resourceClass::slug() . '-seleccion-' . now()->format('Y-m-d-His') . '.pdf';
        $rows = [];

        foreach ($records as $record) {
            $rows[] = $this->rowValues($columns, $record);
        }

        return $this->streamPdf(
            $this->renderHtml($resourceClass::label(), $columns, $rows),
            $filename,
        );
    }

    /**
     * @param array<int, Column> $columns
     * @param Builder<Model> $query
     * @return array<int, array<int, string>>
     */
    private function collectRows(Builder $query, array $columns): array
    {
        $rows = [];

        $query->chunk(200, function ($records) use ($columns, &$rows): void {
            foreach ($records as $record) {
                $rows[] = $this->rowValues($columns, $record);
            }
        });

        return $rows;
    }

    /**
     * @param array<int, Column> $columns
     * @return array<int, string>
     */
    private function rowValues(array $columns, Model $record): array
    {
        return array_map(
            fn (Column $column): string => ExportColumnHelper::formatCellValue($column->resolve($record)),
            $columns,
        );
    }

    /**
     * @param array<int, Column> $columns
     * @param array<int, array<int, string>> $rows
     */
    private function renderHtml(string $title, array $columns, array $rows): string
    {
        return view('panel::exports.resource-pdf', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'exportedAt' => now()->format('Y-m-d H:i'),
        ])->render();
    }

    private function streamPdf(string $html, string $filename): StreamedResponse
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response()->streamDownload(
            static function () use ($dompdf): void {
                echo $dompdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }
}
