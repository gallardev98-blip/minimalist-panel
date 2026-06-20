<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

use Illuminate\Database\Eloquent\Model;

final class BadgeColumn extends Column
{
    /** @var array<string, string> */
    protected array $colors = [];

    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getType(): string
    {
        return 'badge';
    }

    public function resolve(Model $record): mixed
    {
        $value = parent::resolve($record);

        return [
            'value' => $value,
            'color' => $this->colors[(string) $value] ?? 'gray',
        ];
    }

    protected function meta(): array
    {
        return [
            'colors' => $this->colors,
        ];
    }
}
