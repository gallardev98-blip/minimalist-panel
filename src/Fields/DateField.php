<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

final class DateField extends Field
{
    protected bool $withTime = false;

    public function time(bool $withTime = true): static
    {
        $this->withTime = $withTime;

        return $this;
    }

    public function getType(): string
    {
        return $this->withTime ? 'datetime-local' : 'date';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        $value = parent::hydrateForForm($record);

        if ($value instanceof CarbonInterface) {
            return $this->withTime
                ? $value->format('Y-m-d\TH:i')
                : $value->format('Y-m-d');
        }

        if (is_string($value) && $value !== '') {
            return $this->withTime
                ? str_replace(' ', 'T', substr($value, 0, 16))
                : substr($value, 0, 10);
        }

        return $value;
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if ($value === null || $value === '') {
            return ['value' => null, 'include' => true];
        }

        return [
            'value' => is_string($value) ? str_replace('T', ' ', $value) : $value,
            'include' => true,
        ];
    }

    protected function meta(): array
    {
        return [
            'withTime' => $this->withTime,
        ];
    }
}
