<?php

declare(strict_types=1);

namespace Panel\Minimalist\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class MultiSelectFilter extends Filter
{
    /** @var array<string|int, string> */
    protected array $options = [];

    /** @param array<string|int, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /** @param class-string<Model> $model */
    public function relationship(string $model, string $titleColumn = 'name'): static
    {
        $this->options = $model::query()
            ->orderBy($titleColumn)
            ->pluck($titleColumn, 'id')
            ->all();

        return $this;
    }

    public function getType(): string
    {
        return 'multi-select';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value) || $value === []) {
            return $query;
        }

        return $query->whereIn($this->name, $value);
    }

    public function meta(): array
    {
        return [
            'options' => $this->options,
        ];
    }
}
