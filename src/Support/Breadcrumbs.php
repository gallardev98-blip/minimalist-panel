<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Pages\Page;
use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Throwable;

final class Breadcrumbs
{
    /**
     * @return array<int, array{label: string, url: string|null}>
     */
    public static function resolve(?Request $request = null): array
    {
        $request ??= request();
        $route = $request->route();

        if (! $route instanceof Route) {
            return [];
        }

        $crumbs = [
            [
                'label' => __('panel::panel.breadcrumbs.dashboard'),
                'url' => \panel_route('dashboard'),
            ],
        ];

        $name = (string) $route->getName();

        if ($name === 'panel.dashboard') {
            $crumbs[0]['url'] = null;

            return $crumbs;
        }

        if ($name === 'panel.profile') {
            $crumbs[] = [
                'label' => __('panel::panel.profile.title'),
                'url' => null,
            ];

            return $crumbs;
        }

        if ($name === 'panel.pages.show') {
            return self::resolvePageCrumbs($crumbs, $route);
        }

        $resourceSlug = $route->parameter('resource');

        if (! is_string($resourceSlug) || $resourceSlug === '') {
            return $crumbs;
        }

        $resourceClass = app(ResourceRegistry::class)->findBySlug($resourceSlug);

        if ($resourceClass === null) {
            return $crumbs;
        }

        /** @var class-string<Resource> $resourceClass */
        $resourceLabel = $resourceClass::label();
        $indexUrl = \panel_route('resources.index', ['resource' => $resourceSlug]);

        $crumbs[] = [
            'label' => $resourceLabel,
            'url' => $indexUrl,
        ];

        return match ($name) {
            'panel.resources.index' => self::markLast($crumbs),
            'panel.resources.create' => array_merge($crumbs, [[
                'label' => __('panel::panel.breadcrumbs.create'),
                'url' => null,
            ]]),
            'panel.resources.show' => array_merge($crumbs, [[
                'label' => self::recordLabel($resourceClass, $route),
                'url' => null,
            ]]),
            'panel.resources.edit' => array_merge($crumbs, [[
                'label' => __('panel::panel.breadcrumbs.edit'),
                'url' => null,
            ]]),
            default => $crumbs,
        };
    }

    /**
     * @param array<int, array{label: string, url: string|null}> $crumbs
     * @return array<int, array{label: string, url: string|null}>
     */
    private static function resolvePageCrumbs(array $crumbs, Route $route): array
    {
        $pageSlug = $route->parameter('page');

        if (! is_string($pageSlug) || $pageSlug === '') {
            return $crumbs;
        }

        $pageClass = app(PageRegistry::class)->findBySlug($pageSlug);

        if ($pageClass === null) {
            return $crumbs;
        }

        /** @var class-string<Page> $pageClass */
        $crumbs[] = [
            'label' => $pageClass::label(),
            'url' => null,
        ];

        return $crumbs;
    }

    /** @param class-string<Resource> $resourceClass */
    private static function recordLabel(string $resourceClass, Route $route): string
    {
        $recordId = $route->parameter('record');

        if ($recordId === null) {
            return '#';
        }

        try {
            return $resourceClass::recordTitle($resourceClass::findRecord($recordId));
        } catch (Throwable) {
            return '#' . $recordId;
        }
    }

    /**
     * @param array<int, array{label: string, url: string|null}> $crumbs
     * @return array<int, array{label: string, url: string|null}>
     */
    private static function markLast(array $crumbs): array
    {
        if ($crumbs === []) {
            return $crumbs;
        }

        $last = array_key_last($crumbs);
        $crumbs[$last]['url'] = null;

        return $crumbs;
    }
}
