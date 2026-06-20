<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Filters;

use Illuminate\Database\Eloquent\Builder;

final class DateRangeFilter extends Filter
{
    public function getType(): string
    {
        return 'date-range';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value)) {
            return $query;
        }

        $from = $value['from'] ?? null;
        $to = $value['to'] ?? null;

        if (is_string($from) && $from !== '') {
            $query->where($this->name, '>=', $from);
        }

        if (is_string($to) && $to !== '') {
            $query->where($this->name, '<=', $to . ' 23:59:59');
        }

        return $query;
    }

    public function meta(): array
    {
        return [
            'fromLabel' => __('panel::panel.filter_from'),
            'toLabel' => __('panel::panel.filter_to'),
        ];
    }
}
