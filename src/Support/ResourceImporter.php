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
     * @return array{imported: int, updated: int, failed: int, errors: list<string>}
     */
    public function fromPath(string $resourceClass, string $path, string $extension): array
    {
        $analysis = $this->analyzePath($resourceClass, $path, $extension);

        if ($analysis['rows'] === [] && $analysis['errors'] !== []) {
            return ['imported' => 0, 'updated' => 0, 'failed' => 0, 'errors' => $analysis['errors']];
        }

        $payloads = array_values(array_filter(
            array_map(fn (array $row): ?array => $row['valid'] ? $row['payload'] : null, $analysis['rows']),
        ));

        return $this->importPayloads($resourceClass, $payloads);
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @return array{
     *     headers: list<string>,
     *     fields: list<string>,
     *     rows: list<array{row: int, cells: list<string>, payload: array<string, mixed>|null, valid: bool, error: string|null}>,
     *     summary: array{total: int, valid: int, invalid: int},
     *     errors: list<string>
     * }
     */
    public function analyzePath(string $resourceClass, string $path, string $extension): array
    {
        abort_unless($resourceClass::authorize('create'), 403);

        $fields = ImportColumnHelper::importableFields($resourceClass);

        if ($fields === []) {
            return [
                'headers' => [],
                'fields' => [],
                'rows' => [],
                'summary' => ['total' => 0, 'valid' => 0, 'invalid' => 0],
                'errors' => [__('panel::panel.import.no_fields')],
            ];
        }

        $fieldLabels = array_map(fn (Field $field): string => $field->getLabel(), $fields);
        $rows = $this->readRows($path, $extension);

        if ($rows === []) {
            return [
                'headers' => [],
                'fields' => $fieldLabels,
                'rows' => [],
                'summary' => ['total' => 0, 'valid' => 0, 'invalid' => 0],
                'errors' => [__('panel::panel.import.empty_file')],
            ];
        }

        $headers = array_shift($rows);
        $mapping = ImportColumnHelper::mapHeaders($fields, $headers);
        $analyzed = [];
        $valid = 0;
        $invalid = 0;

        foreach ($rows as $line => $cells) {
            if ($this->isEmptyRow($cells)) {
                continue;
            }

            $rowNumber = $line + 2;
            $analyzed[] = $this->analyzeRow($resourceClass, $fields, $mapping, $cells, $rowNumber);
            $analyzed[array_key_last($analyzed)]['valid'] ? $valid++ : $invalid++;
        }

        return [
            'headers' => $headers,
            'fields' => $fieldLabels,
            'rows' => $analyzed,
            'summary' => ['total' => count($analyzed), 'valid' => $valid, 'invalid' => $invalid],
            'errors' => [],
        ];
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @param list<array<string, mixed>> $payloads
     * @return array{imported: int, updated: int, failed: int, errors: list<string>}
     */
    public function importPayloads(string $resourceClass, array $payloads): array
    {
        abort_unless($resourceClass::authorize('create'), 403);

        $fields = ImportColumnHelper::importableFields($resourceClass);
        $imported = 0;
        $updated = 0;
        $failed = 0;
        $errors = [];

        foreach ($payloads as $index => $payload) {
            $result = $this->persistRow($resourceClass, $fields, $payload, $index + 1);

            if ($result === null) {
                $imported++;

                continue;
            }

            if ($result === '__upsert_updated__') {
                $updated++;

                continue;
            }

            $failed++;
            $errors[] = $result;
        }

        return compact('imported', 'updated', 'failed', 'errors');
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
     * @return array{row: int, cells: list<string>, payload: array<string, mixed>|null, valid: bool, error: string|null}
     */
    private function analyzeRow(
        string $resourceClass,
        array $fields,
        array $mapping,
        array $cells,
        int $rowNumber,
    ): array {
        $payload = $this->buildRowPayload($fields, $mapping, $cells);

        if ($payload === null) {
            return [
                'row' => $rowNumber,
                'cells' => $cells,
                'payload' => null,
                'valid' => false,
                'error' => __('panel::panel.import.row_no_columns', ['row' => $rowNumber]),
            ];
        }

        $error = $this->validatePayload($resourceClass, $fields, $payload, $rowNumber);

        return [
            'row' => $rowNumber,
            'cells' => $cells,
            'payload' => $payload,
            'valid' => $error === null,
            'error' => $error,
        ];
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
    private function validatePayload(string $resourceClass, array $fields, array $payload, int $rowNumber): ?string
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

        return null;
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @param array<int, Field> $fields
     * @param array<string, mixed> $payload
     */
    private function persistRow(string $resourceClass, array $fields, array $payload, int $rowNumber): ?string
    {
        $error = $this->validatePayload($resourceClass, $fields, $payload, $rowNumber);

        if ($error !== null) {
            return $error;
        }

        $rules = [];

        foreach ($fields as $field) {
            $rules[$field->getName()] = $field->getRules();
        }

        $validated = Validator::make($payload, $rules, $resourceClass::validationMessages())->validated();
        $stored = FieldPayload::fromValidated($fields, $validated);
        $modelClass = $resourceClass::modelClass();
        $claveUpsert = $this->claveUpsert($resourceClass, $stored);

        if ($claveUpsert !== null) {
            $existente = $modelClass::query()->where($claveUpsert, $stored[$claveUpsert] ?? null)->first();

            if ($existente !== null) {
                if (! $resourceClass::authorize('update', $existente)) {
                    return __('panel::panel.import.upsert_denied', ['row' => $rowNumber]);
                }

                $existente->update($stored);
                FieldPayload::persistAfterSave($fields, $validated, $existente);

                return '__upsert_updated__';
            }
        }

        $record = $modelClass::query()->create($stored);
        FieldPayload::persistAfterSave($fields, $validated, $record);

        return null;
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @param array<string, mixed> $stored
     */
    private function claveUpsert(string $resourceClass, array $stored): ?string
    {
        if (! (bool) config('panel.import.upsert', false)) {
            return null;
        }

        $clave = $resourceClass::importUpsertKey() ?? config('panel.import.upsert_key');

        if (! is_string($clave) || $clave === '' || ! array_key_exists($clave, $stored)) {
            return null;
        }

        $valor = $stored[$clave];

        if ($valor === null || $valor === '') {
            return null;
        }

        return $clave;
    }
}
