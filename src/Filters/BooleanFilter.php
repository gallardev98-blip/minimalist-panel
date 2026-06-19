<?php

declare(strict_types=1);

namespace Panel\Minimalist\Filters;

use Illuminate\Database\Eloquent\Builder;

final class BooleanFilter extends Filter
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($value === null || $value === '') {
            return $query;
        }

        return $query->where($this->name, filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function meta(): array
    {
        return [
            'options' => [
                '' => __('panel::panel.all'),
                '1' => __('panel::panel.yes'),
                '0' => __('panel::panel.no'),
            ],
        ];
    }
}
