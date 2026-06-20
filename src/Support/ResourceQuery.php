<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Columns\Column;
use MyLaravelTools\Panel\Filters\Filter;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ResourceQuery
{
    /** @param class-string<Resource> $resourceClass */
    public function __construct(
        private readonly string $resourceClass,
    ) {}

    /**
     * @param array<int, Column> $columns
     * @param array<int, Filter> $filters
     * @param array<string, mixed> $filterValues
     * @return Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function build(
        array $columns,
        array $filters,
        array $filterValues,
        string $search = '',
        string $sortColumn = '',
        string $sortDirection = 'asc',
        string $trashed = '',
    ): Builder {
        $query = $this->resourceClass::modelClass()::query()
            ->with($this->resourceClass::with());

        $this->applyTrashed($query, $trashed);
        $this->applySearch($query, $columns, $search);
        $this->applyFilters($query, $filters, $filterValues);
        $this->applySorting($query, $columns, $sortColumn, $sortDirection);

        return $query;
    }

    /**
     * @param array<int, Column> $columns
     * @param array<int, Filter> $filters
     * @param array<string, mixed> $filterValues
     */
    public function paginate(
        array $columns,
        array $filters,
        array $filterValues,
        string $search = '',
        string $sortColumn = '',
        string $sortDirection = 'asc',
        string $trashed = '',
    ): LengthAwarePaginator {
        return $this->build($columns, $filters, $filterValues, $search, $sortColumn, $sortDirection, $trashed)
            ->paginate((int) config('panel.per_page', 15));
    }

    private function applyTrashed(Builder $query, string $trashed): void
    {
        if (! $this->resourceClass::usesSoftDeletes()) {
            return;
        }

        match ($trashed) {
            'only' => $query->onlyTrashed(),
            'with' => $query->withTrashed(),
            default => null,
        };
    }

    /** @param array<int, Column> $columns */
    private function applySearch(Builder $query, array $columns, string $search): void
    {
        if ($search === '') {
            return;
        }

        $searchableColumns = array_filter($columns, fn (Column $column): bool => $column->isSearchable());

        if ($searchableColumns === []) {
            return;
        }

        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        $query->where(function (Builder $builder) use ($searchableColumns, $escaped): void {
            foreach ($searchableColumns as $column) {
                $builder->orWhere($column->getName(), 'like', '%' . $escaped . '%');
            }
        });
    }

    /** @param array<int, Filter> $filters */
    private function applyFilters(Builder $query, array $filters, array $filterValues): void
    {
        foreach ($filters as $filter) {
            $value = $filterValues[$filter->getName()] ?? null;
            $filter->apply($query, $value);
        }
    }

    /** @param array<int, Column> $columns */
    private function applySorting(Builder $query, array $columns, string $sortColumn, string $sortDirection): void
    {
        if ($sortColumn === '' || ! $this->isSortable($columns, $sortColumn)) {
            $query->latest('id');

            return;
        }

        $query->orderBy($sortColumn, $sortDirection === 'desc' ? 'desc' : 'asc');
    }

    /** @param array<int, Column> $columns */
    private function isSortable(array $columns, string $sortColumn): bool
    {
        foreach ($columns as $column) {
            if ($column->getName() === $sortColumn && $column->isSortable()) {
                return true;
            }
        }

        return false;
    }
}
