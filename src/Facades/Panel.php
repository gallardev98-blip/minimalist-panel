<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Facades;

use MyLaravelTools\Panel\Support\ResourceRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<int, class-string<\MyLaravelTools\Panel\Resources\Resource>> all()
 * @method static class-string<\MyLaravelTools\Panel\Resources\Resource>|null findBySlug(string $slug)
 * @method static void register(array<int, class-string<\MyLaravelTools\Panel\Resources\Resource>> $resources)
 *
 * @see ResourceRegistry
 */
final class Panel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ResourceRegistry::class;
    }
}
