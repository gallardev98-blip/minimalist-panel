<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Fields\Field;
use Illuminate\Database\Eloquent\Model;

final class FieldPayload
{
    /**
     * @param array<int, Field> $fields
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    public static function fromValidated(array $fields, array $validated, ?Model $record = null): array
    {
        $payload = [];

        foreach ($fields as $field) {
            $name = $field->getName();
            $result = $field->dehydrateForStorage($validated[$name] ?? null, $record);

            if ($result['include']) {
                $payload[$name] = $result['value'];
            }
        }

        return $payload;
    }

    /**
     * @param array<int, Field> $fields
     * @return array<string, mixed>
     */
    public static function initialState(array $fields, ?Model $record = null): array
    {
        $state = [];

        foreach ($fields as $field) {
            $state[$field->getName()] = $field->hydrateForForm($record);
            $state = $field->augmentFormState($state);
        }

        return $state;
    }
}
