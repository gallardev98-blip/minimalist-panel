<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Pages\Page;
use MyLaravelTools\Panel\Resources\Resource;

final class NavigationBuilder
{
    /**
     * @param array<int, array<string, mixed>>|null $navigation
     * @return array<int, array<string, mixed>>
     */
    public static function build(?array $navigation, ResourceRegistry $registry): array
    {
        if ($navigation === null) {
            return self::fromResources($registry);
        }

        $pageRegistry = app(PageRegistry::class);

        $items = array_map(
            fn (array $item): array => self::normalizeItem($item, $registry, $pageRegistry),
            $navigation,
        );

        return array_values(array_filter(
            array_map(fn (array $item): ?array => self::filterItem($item), $items),
        ));
    }

    /** @return array<int, array<string, mixed>> */
    public static function fromResources(ResourceRegistry $registry): array
    {
        $items = [];

        foreach ($registry->all() as $resourceClass) {
            if ($resourceClass::hiddenFromNavigation() || ! $resourceClass::authorize('viewAny')) {
                continue;
            }

            $items[] = self::resourceLink($resourceClass);
        }

        return $items;
    }

    /** @param class-string<Resource> $resourceClass */
    public static function resourceLink(string $resourceClass): array
    {
        return [
            'type' => 'link',
            'label' => $resourceClass::label(),
            'slug' => $resourceClass::slug(),
            'icon' => $resourceClass::icon(),
            'url' => \panel_route('resources.index', ['resource' => $resourceClass::slug()]),
            'badge' => $resourceClass::navigationBadge(),
        ];
    }

    /** @param class-string<Page> $pageClass */
    public static function pageLink(string $pageClass): array
    {
        return [
            'type' => 'link',
            'label' => $pageClass::label(),
            'slug' => $pageClass::slug(),
            'icon' => $pageClass::icon(),
            'url' => $pageClass::url(),
            'badge' => method_exists($pageClass, 'navigationBadge') ? $pageClass::navigationBadge() : null,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function flatten(array $navigation): array
    {
        $flat = [];

        foreach ($navigation as $item) {
            if (($item['type'] ?? 'link') === 'group') {
                foreach ($item['children'] ?? [] as $child) {
                    $flat[] = $child;
                }

                continue;
            }

            $flat[] = $item;
        }

        return $flat;
    }

    /** @param array<string, mixed> $item */
    private static function normalizeItem(array $item, ResourceRegistry $registry, PageRegistry $pageRegistry): array
    {
        $type = $item['type'] ?? 'link';

        if ($type === 'group') {
            $children = array_values(array_map(
                fn (array $child): array => self::normalizeChild($child, $registry, $pageRegistry),
                $item['children'] ?? [],
            ));

            return [
                'type' => 'group',
                'label' => (string) ($item['label'] ?? ''),
                'icon' => $item['icon'] ?? 'folder',
                'open' => self::groupShouldOpen(
                    $children,
                    array_key_exists('open', $item) ? (bool) $item['open'] : null,
                ),
                'children' => $children,
            ];
        }

        return self::normalizeChild($item, $registry, $pageRegistry);
    }

    /** @param array<string, mixed> $item */
    private static function normalizeChild(array $item, ResourceRegistry $registry, PageRegistry $pageRegistry): array
    {
        if (isset($item['resource']) && is_string($item['resource']) && is_subclass_of($item['resource'], Resource::class)) {
            return self::resourceLink($item['resource']);
        }

        if (isset($item['page']) && is_string($item['page']) && is_subclass_of($item['page'], Page::class)) {
            return self::pageLink($item['page']);
        }

        $url = $item['url'] ?? null;

        if ((! is_string($url) || $url === '') && isset($item['route']) && is_string($item['route'])) {
            $url = route($item['route'], $item['route_parameters'] ?? []);
        }

        return [
            'type' => 'link',
            'label' => (string) ($item['label'] ?? ''),
            'slug' => $item['slug'] ?? null,
            'icon' => $item['icon'] ?? 'circle',
            'url' => is_string($url) && $url !== '' ? $url : '#',
            'badge' => $item['badge'] ?? null,
            'permission' => $item['permission'] ?? null,
        ];
    }

    /** @param array<string, mixed> $item */
    private static function filterItem(array $item): ?array
    {
        if (($item['type'] ?? 'link') === 'group') {
            $children = array_values(array_filter(
                $item['children'] ?? [],
                fn (array $child): bool => self::childIsVisible($child),
            ));

            if ($children === []) {
                return null;
            }

            $item['children'] = $children;

            return $item;
        }

        return self::childIsVisible($item) ? $item : null;
    }

    /** @param array<string, mixed> $item */
    private static function childIsVisible(array $item): bool
    {
        if (isset($item['permission']) && is_string($item['permission']) && ! PanelPermission::check($item['permission'])) {
            return false;
        }

        $slug = $item['slug'] ?? null;
        $url = $item['url'] ?? '';

        if (is_string($url) && str_contains($url, '/pages/')) {
            $pageClass = app(PageRegistry::class)->findBySlug((string) $slug);

            return $pageClass !== null && $pageClass::canAccess();
        }

        if (is_string($url) && str_contains($url, '/resources/')) {
            if (! is_string($slug) || $slug === '') {
                return true;
            }

            $resourceClass = app(ResourceRegistry::class)->findBySlug($slug);

            return $resourceClass !== null && $resourceClass::authorize('viewAny');
        }

        return true;
    }

    /** @param array<int, array<string, mixed>> $children */
    private static function groupShouldOpen(array $children, ?bool $explicitOpen = null): bool
    {
        if ($explicitOpen !== null) {
            return $explicitOpen;
        }

        if (config('panel.navigation_groups_expanded', false)) {
            return true;
        }

        foreach ($children as $child) {
            if (self::linkIsCurrent($child)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $item */
    public static function groupHasCurrentChild(array $item): bool
    {
        foreach ($item['children'] ?? [] as $child) {
            if (self::linkIsCurrent($child)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $item */
    public static function linkIsCurrent(array $item): bool
    {
        $slug = $item['slug'] ?? null;

        if (is_string($slug) && $slug !== '' && request()->routeIs('panel.pages.*')) {
            return request()->route('page') === $slug;
        }

        if (is_string($slug) && $slug !== '' && request()->routeIs('panel.resources.*')) {
            return request()->route('resource') === $slug;
        }

        if (request()->routeIs('panel.dashboard')) {
            return false;
        }

        $url = $item['url'] ?? '';

        if (! is_string($url) || $url === '' || $url === '#') {
            return false;
        }

        return url()->current() === $url;
    }
}
