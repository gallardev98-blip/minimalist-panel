<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire\Concerns;

use Panel\Minimalist\Resources\Resource;
use Panel\Minimalist\Support\ResourceAuthorizer;
use Panel\Minimalist\Support\ResourceRegistry;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait InteractsWithPanelResource
{
    /** @var class-string<Resource> */
    public string $resourceClass = '';

    /** @return class-string<Resource> */
    protected function resolveResource(string $slug, string $ability, ?Model $record = null): string
    {
        $resourceClass = app(ResourceRegistry::class)->findBySlug($slug);

        if ($resourceClass === null) {
            throw new NotFoundHttpException("Resource [{$slug}] not found.");
        }

        abort_unless(
            app(ResourceAuthorizer::class)->authorize($resourceClass, $ability, $record),
            403,
        );

        return $resourceClass;
    }

    /** @return array<string, mixed> */
    protected function sharedPanelData(): array
    {
        return [
            'navigation' => app(ResourceRegistry::class)->navigation(),
            'brandName' => config('panel.brand.name', 'Panel'),
        ];
    }
}
