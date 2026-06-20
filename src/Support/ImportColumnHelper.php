<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Fields\BelongsToField;
use MyLaravelTools\Panel\Fields\Field;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

final class ImportColumnHelper
{
    /** @var list<string> */
    private const EXCLUDED_TYPES = [
        'image',
        'file',
        'password',
        'permissions',
        'roles',
        'rich-text',
    ];

    /** @param class-string<Resource> $resourceClass */
    /** @return array<int, Field> */
    public static function importableFields(string $resourceClass): array
    {
        $schema = $resourceClass::import();
        $fields = FormSchema::fields($schema !== [] ? $schema : $resourceClass::form());

        return array_values(array_filter(
            $fields,
            fn (Field $field): bool => $field->isImportable()
                && ! in_array($field->getType(), self::EXCLUDED_TYPES, true)
                && ! $field->isDisabled(),
        ));
    }

    /**
     * @param array<int, Field> $fields
     * @param list<string> $headers
     * @return array<int, Field|null>
     */
    public static function mapHeaders(array $fields, array $headers): array
    {
        $lookup = [];

        foreach ($fields as $field) {
            $lookup[self::normalizeKey($field->getName())] = $field;
            $lookup[self::normalizeKey($field->getLabel())] = $field;
        }

        $mapped = [];

        foreach ($headers as $index => $header) {
            $mapped[$index] = $lookup[self::normalizeKey($header)] ?? null;
        }

        return $mapped;
    }

    public static function parseCellValue(Field $field, mixed $raw): mixed
    {
        if ($raw === null || $raw === '') {
            return $field->getType() === 'boolean' ? false : null;
        }

        $value = is_string($raw) ? trim($raw) : $raw;

        return match ($field->getType()) {
            'boolean' => in_array(strtolower((string) $value), ['1', 'true', 'yes', 'sí', 'si'], true),
            'number' => is_numeric($value) ? $value + 0 : $value,
            'belongs-to' => self::resolveBelongsTo($field, $value),
            default => $value,
        };
    }

    public static function formatCellForTemplate(Field $field, Model $record): string
    {
        $value = data_get($record, $field->getName());

        if ($field instanceof BelongsToField && $value !== null && $value !== '') {
            $options = $field->resolveOptions();

            return (string) ($options[$value] ?? $value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) ($value ?? '');
    }

    private static function normalizeKey(string $value): string
    {
        return strtolower(trim($value));
    }

    private static function resolveBelongsTo(Field $field, mixed $value): mixed
    {
        if (! $field instanceof BelongsToField) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $meta = $field->toArray()['meta'] ?? [];
        $options = $meta['options'] ?? [];

        foreach ($options as $id => $label) {
            if (strcasecmp((string) $label, (string) $value) === 0) {
                return $id;
            }
        }

        return $value;
    }
}
