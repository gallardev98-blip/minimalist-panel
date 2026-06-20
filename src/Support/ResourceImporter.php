<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class ResourceImporter
{
    /**
     * @param class-string<Resource> $resourceClass
     * @return array{imported: int, failed: int, errors: list<string>}
     */
    public function fromPath(string $resourceClass, string $path, string $extension): array
    {
        abort_unless($resourceClass::authorize('create'), 403);

        $fields = ImportColumnHelper::importableFields($resourceClass);

        if ($fields === []) {
            return ['imported' => 0, 'failed' => 0, 'errors' => [__('panel::panel.import.no_fields')]];
        }

        $rows = $this->readRows($path, $extension);

        if ($rows === []) {
            return ['imported' => 0, 'failed' => 0, 'errors' => [__('panel::panel.import.empty_file')]];
        }

        $headers = array_shift($rows);
        $mapping = ImportColumnHelper::mapHeaders($fields, $headers);
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $line => $cells) {
            if ($this->isEmptyRow($cells)) {
                continue;
            }

            $rowNumber = $line + 2;
            $payload = $this->buildRowPayload($fields, $mapping, $cells);

            if ($payload === null) {
                $failed++;
                $errors[] = __('panel::panel.import.row_no_columns', ['row' => $rowNumber]);

                continue;
            }

            $result = $this->persistRow($resourceClass, $fields, $payload, $rowNumber);

            if ($result === null) {
                $imported++;

                continue;
            }

            $failed++;
            $errors[] = $result;
        }

        return compact('imported', 'failed', 'errors');
    }

    /** @return list<list<string>> */
    private function readRows(string $path, string $extension): array
    {
        return in_array(strtolower($extension), ['xlsx', 'xls'], true)
            ? $this->readExcel($path)
            : $this->readCsv($path);
    }

    /** @return list<list<string>> */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_map(
                fn (mixed $cell): string => is_string($cell) ? trim($cell) : (string) $cell,
                $data,
            );
        }

        fclose($handle);

        return $rows;
    }

    /** @return list<list<string>> */
    private function readExcel(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = [];

        foreach ($sheet->toArray(null, true, true, false) as $row) {
            $rows[] = array_map(
                fn (mixed $cell): string => trim((string) ($cell ?? '')),
                $row,
            );
        }

        return $rows;
    }

    /** @param list<string> $cells */
    private function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            if ($cell !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int, Field> $fields
     * @param array<int, Field|null> $mapping
     * @param list<string> $cells
     * @return array<string, mixed>|null
     */
    private function buildRowPayload(array $fields, array $mapping, array $cells): ?array
    {
        $payload = [];
        $hasMapped = false;

        foreach ($mapping as $index => $field) {
            if ($field === null) {
                continue;
            }

            $hasMapped = true;
            $payload[$field->getName()] = ImportColumnHelper::parseCellValue(
                $field,
                $cells[$index] ?? '',
            );
        }

        if (! $hasMapped) {
            return null;
        }

        foreach ($fields as $field) {
            $name = $field->getName();

            if (! array_key_exists($name, $payload) && $field->getDefault() !== null) {
                $payload[$name] = $field->getDefault();
            }
        }

        return $payload;
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @param array<int, Field> $fields
     * @param array<string, mixed> $payload
     */
    private function persistRow(string $resourceClass, array $fields, array $payload, int $rowNumber): ?string
    {
        $rules = [];

        foreach ($fields as $field) {
            $rules[$field->getName()] = $field->getRules();
        }

        $validator = Validator::make($payload, $rules, $resourceClass::validationMessages());

        if ($validator->fails()) {
            return __('panel::panel.import.row_failed', [
                'row' => $rowNumber,
                'message' => $validator->errors()->first(),
            ]);
        }

        $validated = $validator->validated();
        $stored = FieldPayload::fromValidated($fields, $validated);
        $modelClass = $resourceClass::modelClass();
        $record = $modelClass::query()->create($stored);
        FieldPayload::persistAfterSave($fields, $validated, $record);

        return null;
    }
}
