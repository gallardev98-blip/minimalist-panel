<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Concerns;

use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\ResourceAuthorizer;
use MyLaravelTools\Panel\Support\ResourceRegistry;
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
