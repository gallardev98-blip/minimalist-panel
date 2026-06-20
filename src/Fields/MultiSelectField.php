<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;

final class MultiSelectField extends Field
{
    /** @var array<string|int, string> */
    protected array $options = [];

    /** @param array<string|int, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return 'multi-select';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        $value = parent::hydrateForForm($record);

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if (! is_array($value)) {
            return ['value' => [], 'include' => true];
        }

        return [
            'value' => array_values(array_filter($value, fn (mixed $item): bool => $item !== '' && $item !== null)),
            'include' => true,
        ];
    }

    protected function meta(): array
    {
        return [
            'options' => $this->options,
        ];
    }
}
