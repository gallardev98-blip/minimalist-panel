<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Pages\Page;
use InvalidArgumentException;

final class PageRegistry
{
    /** @var array<string, array<int, class-string<Page>>> */
    private array $cache = [];

    /** @return array<int, class-string<Page>> */
    public function all(): array
    {
        $id = PanelManager::idActual();

        if (! isset($this->cache[$id])) {
            $this->cache[$id] = $this->resolvePages();
        }

        return $this->cache[$id];
    }

    public function limpiarCache(): void
    {
        $this->cache = [];
    }

    /** @return class-string<Page>|null */
    public function findBySlug(string $slug): ?string
    {
        foreach ($this->all() as $pageClass) {
            if ($pageClass::slug() === $slug) {
                return $pageClass;
            }
        }

        return null;
    }

    /** @return array<int, class-string<Page>> */
    private function resolvePages(): array
    {
        $configured = config('panel.pages.registered', []);
        $discovered = app(PageDiscovery::class)->discover();

        return $this->normalize(array_merge($discovered, $configured));
    }

    /** @param array<int, class-string<Page>> $pages */
    private function normalize(array $pages): array
    {
        $normalized = [];

        foreach ($pages as $pageClass) {
            if (! is_subclass_of($pageClass, Page::class)) {
                throw new InvalidArgumentException("{$pageClass} must extend " . Page::class);
            }

            $normalized[] = $pageClass;
        }

        return array_values(array_unique($normalized));
    }
}
