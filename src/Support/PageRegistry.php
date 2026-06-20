<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Pages\Page;
use InvalidArgumentException;

final class PageRegistry
{
    /** @var array<int, class-string<Page>> */
    private array $pages;

    public function __construct()
    {
        $this->pages = $this->resolvePages();
    }

    /** @return array<int, class-string<Page>> */
    public function all(): array
    {
        return $this->pages;
    }

    /** @return class-string<Page>|null */
    public function findBySlug(string $slug): ?string
    {
        foreach ($this->pages as $pageClass) {
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
