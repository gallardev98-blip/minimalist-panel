<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire;

use Panel\Minimalist\Actions\BulkAction;
use Panel\Minimalist\Actions\RowAction;
use Panel\Minimalist\Livewire\Concerns\ConfirmsPanelActions;
use Panel\Minimalist\Livewire\Concerns\DispatchesPanelToasts;
use Panel\Minimalist\Livewire\Concerns\InteractsWithPanelResource;
use Panel\Minimalist\Livewire\Concerns\ManagesResourceFormModal;
use Panel\Minimalist\Resources\Resource;
use Panel\Minimalist\Support\CsvExporter;
use Panel\Minimalist\Support\ExcelExporter;
use Panel\Minimalist\Support\FormSchema;
use Panel\Minimalist\Support\PdfExporter;
use Panel\Minimalist\Support\ResourceQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('panel::layouts.app')]
final class ResourceIndex extends Component
{
    use ConfirmsPanelActions;
    use DispatchesPanelToasts;
    use InteractsWithPanelResource;
    use ManagesResourceFormModal;
    use WithFileUploads;
    use WithPagination;

    public string $resource;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortColumn = '';

    #[Url(as: 'dir')]
    public string $sortDirection = 'asc';

    #[Url(as: 'trashed')]
    public string $trashed = '';

    /** @var array<string, mixed> */
    #[Url(as: 'filters')]
    public array $filterValues = [];

    /** @var array<int, int|string> */
    public array $selected = [];

    public function mount(string $resource): void
    {
        $this->resource = $resource;
        $this->resourceClass = $this->resolveResource($resource, 'viewAny');
        $this->initializeFilterDefaults();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterValues(): void
    {
        $this->resetPage();
    }

    public function updatedTrashed(): void
    {
        $this->resetPage();
        $this->selected = [];
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    public function requestRowAction(string $actionName, int|string $recordId): void
    {
        $action = $this->findRowAction($actionName);

        if ($action === null) {
            $this->toastError(__('panel::panel.invalid_action'));

            return;
        }

        if ($action->getConfirmation() !== null) {
            $this->askConfirm($action->getConfirmation(), 'row', $recordId, rowAction: $actionName);

            return;
        }

        $this->runRowActionWithoutConfirm($actionName, $recordId);
    }

    public function runRowActionWithoutConfirm(string $actionName, int|string $recordId): void
    {
        $action = $this->findRowAction($actionName);

        if ($action === null) {
            return;
        }

        $record = $this->resourceClass::findRecord($recordId, $this->trashed === 'only' || $actionName === 'restore' || $actionName === 'forceDelete');

        if (! $action->isVisible($record, $this->resourceClass)) {
            $this->toastError(__('panel::panel.unauthorized_action'));

            return;
        }

        match ($actionName) {
            'delete' => $this->delete($recordId),
            'restore' => $this->restore($recordId),
            'forceDelete' => $this->forceDelete($recordId),
            default => tap(null, function () use ($action, $record): void {
                $action->run($record);
                $this->toastSuccess(__('panel::panel.action_completed'));
            }),
        };
    }

    public function delete(int|string $recordId): void
    {
        $record = $this->resourceClass::findRecord($recordId, $this->trashed === 'only');

        abort_unless($this->resourceClass::authorize('delete', $record), 403);

        $record->delete();
        $this->toastSuccess(__('panel::panel.record_deleted'));
    }

    public function restore(int|string $recordId): void
    {
        $record = $this->resourceClass::findRecord($recordId, true);

        abort_unless($this->resourceClass::authorize('restore', $record), 403);

        $record->restore();
        $this->toastSuccess(__('panel::panel.record_restored'));
    }

    public function forceDelete(int|string $recordId): void
    {
        $record = $this->resourceClass::findRecord($recordId, true);

        abort_unless($this->resourceClass::authorize('forceDelete', $record), 403);

        $record->forceDelete();
        $this->toastSuccess(__('panel::panel.record_force_deleted'));
    }

    public function toggleSelectAll(): void
    {
        $records = $this->paginateRecords();
        $ids = $records->pluck('id')->all();

        $this->selected = count($this->selected) === count($ids) ? [] : $ids;
    }

    public function runBulkAction(string $actionName): mixed
    {
        $action = $this->findBulkAction($actionName);

        if ($action === null) {
            $this->toastError(__('panel::panel.invalid_action'));

            return null;
        }

        if ($action->getConfirmation() !== null) {
            $this->askConfirm($action->getConfirmation(), 'bulk', bulkAction: $actionName);

            return null;
        }

        return $this->runBulkActionWithoutConfirm($actionName);
    }

    public function runBulkActionWithoutConfirm(string $actionName): mixed
    {
        $action = $this->findBulkAction($actionName);

        if ($action === null) {
            $this->toastError(__('panel::panel.invalid_action'));

            return null;
        }

        $records = $this->selectedRecords();

        if ($records->isEmpty()) {
            $this->toastError(__('panel::panel.select_at_least_one'));

            return null;
        }

        foreach ($records as $record) {
            if (! $this->authorizeBulkRecord($actionName, $record)) {
                $this->toastError(__('panel::panel.unauthorized_action'));

                return null;
            }
        }

        if ($actionName === 'exportSelection') {
            $this->selected = [];

            return app(CsvExporter::class)->downloadSelected($this->resourceClass, $records);
        }

        if ($actionName === 'exportSelectionExcel') {
            $this->selected = [];

            return app(ExcelExporter::class)->downloadSelected($this->resourceClass, $records);
        }

        if ($actionName === 'exportSelectionPdf') {
            $this->selected = [];

            return app(PdfExporter::class)->downloadSelected($this->resourceClass, $records);
        }

        $action->run($records);
        $this->selected = [];
        $this->toastSuccess(__('panel::panel.action_completed'));

        return null;
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless($this->resourceClass::authorize('viewAny'), 403);

        if ($this->selected !== []) {
            return $this->exportSelectedCsv();
        }

        $query = (new ResourceQuery($this->resourceClass))->build(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
        );

        return app(CsvExporter::class)->download($this->resourceClass, $query);
    }

    public function exportExcel(): StreamedResponse
    {
        abort_unless($this->resourceClass::authorize('viewAny'), 403);

        if ($this->selected !== []) {
            return $this->exportSelectedExcel();
        }

        $query = (new ResourceQuery($this->resourceClass))->build(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
        );

        return app(ExcelExporter::class)->download($this->resourceClass, $query);
    }

    public function exportPdf(): StreamedResponse
    {
        abort_unless($this->resourceClass::authorize('viewAny'), 403);

        if ($this->selected !== []) {
            return $this->exportSelectedPdf();
        }

        $query = (new ResourceQuery($this->resourceClass))->build(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
        );

        return app(PdfExporter::class)->download($this->resourceClass, $query);
    }

    private function exportSelectedCsv(): StreamedResponse
    {
        $records = $this->authorizedSelectedRecords();
        $this->selected = [];

        return app(CsvExporter::class)->downloadSelected($this->resourceClass, $records);
    }

    private function exportSelectedExcel(): StreamedResponse
    {
        $records = $this->authorizedSelectedRecords();
        $this->selected = [];

        return app(ExcelExporter::class)->downloadSelected($this->resourceClass, $records);
    }

    private function exportSelectedPdf(): StreamedResponse
    {
        $records = $this->authorizedSelectedRecords();
        $this->selected = [];

        return app(PdfExporter::class)->downloadSelected($this->resourceClass, $records);
    }

    public function render(): mixed
    {
        /** @var class-string<Resource> $resourceClass */
        $resourceClass = $this->resourceClass;

        $formSchema = $resourceClass::form();

        return view('panel::livewire.resource-index', array_merge($this->sharedPanelData(), [
            'resourceSlug' => $this->resource,
            'resourceClass' => $resourceClass,
            'resourceLabel' => $resourceClass::label(),
            'canCreate' => $resourceClass::authorize('create'),
            'usesSoftDeletes' => $resourceClass::usesSoftDeletes(),
            'columns' => $resourceClass::table(),
            'filters' => $resourceClass::filters(),
            'rowActions' => $resourceClass::rowActions(),
            'bulkActions' => $this->visibleBulkActions(),
            'records' => $this->paginateRecords(),
            'hasBulkActions' => $this->visibleBulkActions() !== [],
            'selectedCount' => count($this->selected),
            'formsInModal' => $this->formsInModal(),
            'showFormModal' => $this->showFormModal,
            'formRecordId' => $this->formRecordId,
            'formSchema' => $formSchema,
            'hasTabs' => FormSchema::hasTabs($formSchema),
            'hasSections' => FormSchema::hasSections($formSchema),
        ]))->title($resourceClass::label());
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return $this->formModalRules();
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return $this->formModalMessages();
    }

    private function paginateRecords(): mixed
    {
        return (new ResourceQuery($this->resourceClass))->paginate(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
        );
    }

    private function initializeFilterDefaults(): void
    {
        foreach ($this->resourceClass::filters() as $filter) {
            $name = $filter->getName();

            if ($filter->getType() === 'date-range') {
                $this->filterValues[$name] ??= ['from' => '', 'to' => ''];

                continue;
            }

            if ($filter->getType() === 'multi-select') {
                $this->filterValues[$name] ??= [];

                continue;
            }

            $this->filterValues[$name] ??= '';
        }
    }

    /** @return array<int, BulkAction> */
    private function visibleBulkActions(): array
    {
        return array_values(array_filter(
            $this->resourceClass::bulkActions(),
            fn (BulkAction $action): bool => match ($action->getName()) {
                'restore', 'forceDelete' => $this->trashed === 'only' && $this->resourceClass::usesSoftDeletes(),
                'delete' => $this->trashed !== 'only',
                default => true,
            },
        ));
    }

    private function findBulkAction(string $name): ?BulkAction
    {
        foreach ($this->visibleBulkActions() as $action) {
            if ($action->getName() === $name) {
                return $action;
            }
        }

        return null;
    }

    private function findRowAction(string $name): ?RowAction
    {
        foreach ($this->resourceClass::rowActions() as $action) {
            if ($action->getName() === $name) {
                return $action;
            }
        }

        return null;
    }

    /** @return Collection<int, Model> */
    private function selectedRecords(): Collection
    {
        if ($this->selected === []) {
            return new Collection();
        }

        $query = $this->resourceClass::modelClass()::query()
            ->whereIn('id', $this->selected);

        if ($this->trashed === 'only' && $this->resourceClass::usesSoftDeletes()) {
            $query->onlyTrashed();
        }

        return $query->get();
    }

    /** @return Collection<int, Model> */
    private function authorizedSelectedRecords(): Collection
    {
        $records = $this->selectedRecords();

        if ($records->isEmpty()) {
            abort(403);
        }

        foreach ($records as $record) {
            abort_unless($this->resourceClass::authorize('view', $record), 403);
        }

        return $records;
    }

    private function authorizeBulkRecord(string $actionName, Model $record): bool
    {
        return match ($actionName) {
            'delete' => $this->resourceClass::authorize('delete', $record),
            'restore' => $this->resourceClass::authorize('restore', $record),
            'forceDelete' => $this->resourceClass::authorize('forceDelete', $record),
            'exportSelection' => $this->resourceClass::authorize('view', $record),
            'exportSelectionExcel' => $this->resourceClass::authorize('view', $record),
            'exportSelectionPdf' => $this->resourceClass::authorize('view', $record),
            default => true,
        };
    }
}
