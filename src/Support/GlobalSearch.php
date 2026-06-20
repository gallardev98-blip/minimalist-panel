<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

final class GlobalSearch
{
    public function __construct(
        private readonly ResourceRegistry $registry,
    ) {}

    /**
     * @return array<int, array{type: string, label: string, description: string|null, url: string, icon: string|null}>
     */
    public function search(string $query, int $limit = 15): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return $this->navigationResults($query);
        }

        $results = $this->navigationResults($query);
        $remaining = max(0, $limit - count($results));

        if ($remaining === 0) {
            return array_slice($results, 0, $limit);
        }

        foreach ($this->registry->all() as $resourceClass) {
            if (! $resourceClass::authorize('viewAny')) {
                continue;
            }

            $recordResults = $this->searchResource($resourceClass, $query, $remaining);

            foreach ($recordResults as $result) {
                $results[] = $result;
                $remaining--;

                if ($remaining === 0) {
                    break 2;
                }
            }
        }

        return $results;
    }

    /**
     * @return array<int, array{type: string, label: string, description: string|null, url: string, icon: string|null}>
     */
    private function navigationResults(string $query): array
    {
        $normalized = mb_strtolower($query);

        return array_values(array_filter(
            array_map(function (array $item): array {
                return [
                    'type' => 'navigation',
                    'label' => $item['label'],
                    'description' => __('panel::panel.global_search.go_to_resource'),
                    'url' => $item['url'],
                    'icon' => $item['icon'],
                ];
            }, NavigationBuilder::flatten($this->registry->navigation())),
            fn (array $item): bool => $normalized === ''
                || str_contains(mb_strtolower($item['label']), $normalized)
                || str_contains(mb_strtolower($item['url']), $normalized),
        ));
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @return array<int, array{type: string, label: string, description: string|null, url: string, icon: string|null}>
     */
    private function searchResource(string $resourceClass, string $query, int $limit): array
    {
        $columns = $resourceClass::table();
        $builder = (new ResourceQuery($resourceClass))->build(
            columns: $columns,
            filters: $resourceClass::filters(),
            filterValues: [],
            search: $query,
        );

        $records = $builder->limit($limit)->get();
        $results = [];

        foreach ($records as $record) {
            $results[] = [
                'type' => 'record',
                'label' => $this->recordLabel($resourceClass, $record),
                'description' => $resourceClass::label(),
                'url' => route('panel.resources.show', [
                    'resource' => $resourceClass::slug(),
                    'record' => $record->getKey(),
                ]),
                'icon' => $resourceClass::icon(),
            ];
        }

        return $results;
    }

    /** @param class-string<Resource> $resourceClass */
    private function recordLabel(string $resourceClass, Model $record): string
    {
        foreach ($resourceClass::table() as $column) {
            if (! $column->isSearchable()) {
                continue;
            }

            $value = $column->resolve($record);

            if (is_string($value) && $value !== '') {
                return $value;
            }

            if (is_array($value) && isset($value['value']) && is_string($value['value'])) {
                return $value['value'];
            }
        }

        return '#' . $record->getKey();
    }
}
