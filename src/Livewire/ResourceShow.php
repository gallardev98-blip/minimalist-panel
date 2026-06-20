<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire;

use MyLaravelTools\Panel\Livewire\Concerns\InteractsWithPanelResource;
use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\ResourceRegistry;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('panel::layouts.app')]
final class ResourceShow extends Component
{
    use InteractsWithPanelResource;

    public string $resource;

    public int $recordId;

    private Model $record;

    public function mount(string $resource, int|string $record): void
    {
        $resourceClass = app(ResourceRegistry::class)->findBySlug($resource);

        if ($resourceClass === null) {
            throw new NotFoundHttpException("Resource [{$resource}] not found.");
        }

        $this->resource = $resource;
        $this->record = $resourceClass::findRecord($record, $resourceClass::usesSoftDeletes());
        $this->recordId = (int) $record;
        $this->resourceClass = $this->resolveResource($resource, 'view', $this->record);
        $this->record->load($resourceClass::with());
    }

    public function render(): mixed
    {
        /** @var class-string<Resource> $resourceClass */
        $resourceClass = $this->resourceClass;

        return view('panel::livewire.resource-show', array_merge($this->sharedPanelData(), [
            'resourceSlug' => $this->resource,
            'resourceClass' => $resourceClass,
            'resourceLabel' => $resourceClass::label(),
            'recordTitle' => $resourceClass::recordTitle($this->record),
            'record' => $this->record,
            'detailItems' => $resourceClass::detail(),
            'relations' => $resourceClass::relations(),
            'canEdit' => $resourceClass::authorize('update', $this->record),
            'isTrashed' => $resourceClass::usesSoftDeletes() && method_exists($this->record, 'trashed') && $this->record->trashed(),
        ]))->title($resourceClass::recordTitle($this->record));
    }
}
