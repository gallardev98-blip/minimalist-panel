<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

final class DateTimeColumn extends Column
{
    protected string $format = 'd/m/Y H:i';

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getType(): string
    {
        return 'datetime';
    }

    public function resolve(Model $record): mixed
    {
        $value = parent::resolve($record);

        if ($value === null) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format($this->format);
        }

        return Carbon::parse($value)->format($this->format);
    }

    protected function meta(): array
    {
        return [
            'format' => $this->format,
        ];
    }
}
