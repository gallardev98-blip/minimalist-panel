<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Support\Facades\File;
use ReflectionClass;

final class ResourceDiscovery
{
    /** @return array<int, class-string<Resource>> */
    public function discover(): array
    {
        if (! config('panel.discovery.enabled', true)) {
            return [];
        }

        $path = config('panel.discovery.path', app_path('Panel/Resources'));

        if (! is_dir($path)) {
            return [];
        }

        $namespace = rtrim((string) config('panel.discovery.namespace', 'App\\Panel\\Resources'), '\\');
        $resources = [];

        foreach (File::files($path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = $namespace . '\\' . $file->getFilenameWithoutExtension();

            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Resource::class)) {
                continue;
            }

            $resources[] = $class;
        }

        sort($resources);

        return $resources;
    }
}
