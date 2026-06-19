<?php

declare(strict_types=1);

namespace Panel\Minimalist\Facades;

use Panel\Minimalist\Support\ResourceRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<int, class-string<\Panel\Minimalist\Resources\Resource>> all()
 * @method static class-string<\Panel\Minimalist\Resources\Resource>|null findBySlug(string $slug)
 * @method static void register(array<int, class-string<\Panel\Minimalist\Resources\Resource>> $resources)
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
