<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Pages\Page;
use Illuminate\Support\Facades\File;
use ReflectionClass;

final class PageDiscovery
{
    /** @return array<int, class-string<Page>> */
    public function discover(): array
    {
        if (! config('panel.pages.discovery.enabled', true)) {
            return [];
        }

        $path = config('panel.pages.discovery.path', app_path('Panel/Pages'));

        if (! is_dir($path)) {
            return [];
        }

        $namespace = rtrim((string) config('panel.pages.discovery.namespace', 'App\\Panel\\Pages'), '\\');
        $pages = [];

        foreach (File::files($path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = $namespace . '\\' . $file->getFilenameWithoutExtension();

            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Page::class)) {
                continue;
            }

            $pages[] = $class;
        }

        sort($pages);

        return $pages;
    }
}
