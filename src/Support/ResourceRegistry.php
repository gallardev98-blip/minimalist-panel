<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\ResourceDiscovery;
use InvalidArgumentException;

final class ResourceRegistry
{
    /** @var array<int, class-string<Resource>> */
    private array $resources;

  public function __construct()
    {
        $this->resources = $this->resolveResources();
    }

    /** @return array<int, class-string<Resource>> */
    public function all(): array
    {
        return $this->resources;
    }

    /** @return class-string<Resource>|null */
    public function findBySlug(string $slug): ?string
    {
        foreach ($this->resources as $resourceClass) {
            if ($resourceClass::slug() === $slug) {
                return $resourceClass;
            }
        }

        return null;
    }

    /** @return array<int, array<string, mixed>> */
    public function navigation(): array
    {
        return NavigationBuilder::build(config('panel.navigation'), $this);
    }

    /** @return array<int, class-string<Resource>> */
    private function resolveResources(): array
    {
        $configured = config('panel.resources', []);
        $discovered = app(ResourceDiscovery::class)->discover();
        $builtIn = SpatieResourceRegistrar::resources();

        $hostResources = $this->normalize(array_merge($discovered, $configured));
        $seenSlugs = [];
        $resources = [];

        foreach ($hostResources as $resourceClass) {
            $slug = $resourceClass::slug();
            $seenSlugs[$slug] = true;
            $resources[] = $resourceClass;
        }

        foreach ($this->normalize($builtIn) as $resourceClass) {
            $slug = $resourceClass::slug();

            if (isset($seenSlugs[$slug])) {
                continue;
            }

            $seenSlugs[$slug] = true;
            $resources[] = $resourceClass;
        }

        return $resources;
    }

    /** @param array<int, class-string<Resource>> $resources */
    private function normalize(array $resources): array
    {
        $normalized = [];

        foreach ($resources as $resourceClass) {
            if (! is_subclass_of($resourceClass, Resource::class)) {
                throw new InvalidArgumentException("{$resourceClass} must extend " . Resource::class);
            }

            $normalized[] = $resourceClass;
        }

        return array_values(array_unique($normalized));
    }
}
