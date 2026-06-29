<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire;

use MyLaravelTools\Panel\Actions\BulkAction;
use MyLaravelTools\Panel\Actions\RowAction;
use MyLaravelTools\Panel\Livewire\Concerns\ConfirmsPanelActions;
use MyLaravelTools\Panel\Livewire\Concerns\DispatchesPanelToasts;
use MyLaravelTools\Panel\Livewire\Concerns\InteractsWithPanelResource;
use MyLaravelTools\Panel\Livewire\Concerns\ManagesResourceFormModal;
use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\CriteriosActivosIndex;
use MyLaravelTools\Panel\Support\CsvExporter;
use MyLaravelTools\Panel\Support\ExcelExporter;
use MyLaravelTools\Panel\Support\FormSchema;
use MyLaravelTools\Panel\Support\ImportTemplateExporter;
use MyLaravelTools\Panel\Support\PanelLayout;
use MyLaravelTools\Panel\Support\PanelListado;
use MyLaravelTools\Panel\Support\PanelRendimiento;
use MyLaravelTools\Panel\Support\PdfExporter;
use MyLaravelTools\Panel\Support\PanelImpersonation;
use MyLaravelTools\Panel\Support\ResourceQuery;
use Illuminate\Contracts\Pagination\CursorPaginator;
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

    public bool $seleccionGlobal = false;

    public bool $showVistaRapida = false;

    public int|string|null $vistaRapidaId = null;

    public bool $showImportModal = false;

    public string $importStep = 'upload';

    /** @var array<string, mixed>|null */
    public ?array $importPreview = null;

    /** @var array{imported: int, updated: int, failed: int, errors: list<string>, ok: bool}|null */
    public ?array $importResumen = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $importFile = null;

    #[Url(as: 'per_page')]
    public int $perPage = 0;

    public function mount(string $resource): void
    {
        $this->resource = $resource;
        $this->resourceClass = $this->resolveResource($resource, 'viewAny');
        $this->initializeFilterDefaults();

        if ($this->perPage < 1) {
            $this->perPage = (int) config('panel.per_page', 15);
        }
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterValues(): void
    {
        $this->resetPage();
        $this->dispatch(
            'panel-filtros-actualizados',
            filtros: $this->contarFiltrosDeColumnas(),
            hasActive: $this->hasActiveFilters(),
        );
    }

    public function updatedTrashed(): void
    {
        $this->resetPage();
        $this->limpiarSeleccion();
    }

    public function resetFilters(): void
    {
        $this->filterValues = [];
        $this->search = '';
        $this->initializeFilterDefaults();
        $this->resetPage();
        $this->limpiarSeleccion();
        $this->dispatch(
            'panel-filtros-actualizados',
            filtros: 0,
            hasActive: false,
        );
    }

    /** @return list<array{nombre: string, etiqueta: string, valor: string}> */
    public function chipsCriteriosActivos(): array
    {
        return CriteriosActivosIndex::chips($this->search, $this->filterValues, $this->resourceClass);
    }

    public function quitarCriterio(string $nombre): void
    {
        if ($nombre === 'search') {
            $this->search = '';
        } else {
            foreach ($this->resourceClass::filters() as $filtro) {
                if ($filtro->getName() !== $nombre) {
                    continue;
                }

                $this->restablecerFiltro($filtro);
                break;
            }
        }

        $this->resetPage();
        $this->dispatch(
            'panel-filtros-actualizados',
            filtros: $this->contarFiltrosDeColumnas(),
            hasActive: $this->hasActiveFilters(),
        );
    }

    public function abrirRegistro(int|string $id): void
    {
        if (! PanelLayout::filasClicables()) {
            return;
        }

        $registro = $this->resourceClass::findRecord($id, $this->trashed === 'only');
        $accion = $this->findRowAction('edit');

        if ($accion === null || ! $accion->isVisible($registro, $this->resourceClass)) {
            return;
        }

        if ($this->formsInModal()) {
            $this->openEditFormModal($id);

            return;
        }

        $url = $accion->resolveUrl($registro, $this->resource);

        if ($url !== null) {
            $this->redirect($url, navigate: true);
        }
    }

    public function limpiarSeleccion(): void
    {
        $this->selected = [];
        $this->seleccionGlobal = false;
    }

    public function updatedSelected(): void
    {
        $this->seleccionGlobal = false;
    }

    public function seleccionarTodosLosResultados(): void
    {
        if (! PanelLayout::seleccionGlobalActiva()) {
            return;
        }

        $total = $this->contarRegistrosFiltrados();
        $maximo = PanelLayout::maximoSeleccionGlobal();

        if ($total > $maximo) {
            $this->toastError(__('panel::panel.select_all_limit', ['max' => $maximo, 'total' => $total]));

            return;
        }

        if ($total < 1) {
            return;
        }

        $this->seleccionGlobal = true;
    }

    public function abrirVistaRapida(int|string $id): void
    {
        if (! PanelLayout::vistaRapida()) {
            return;
        }

        $registro = $this->resourceClass::findRecord($id, $this->trashed === 'only');

        abort_unless($this->resourceClass::authorize('view', $registro), 403);

        $this->vistaRapidaId = $id;
        $this->showVistaRapida = true;
    }

    public function cerrarVistaRapida(): void
    {
        $this->showVistaRapida = false;
        $this->vistaRapidaId = null;
    }

    public function contarSeleccion(mixed $paginador): int
    {
        if ($this->seleccionGlobal) {
            return $this->totalRegistros($paginador);
        }

        return count($this->selected);
    }

    public function mostrarSeleccionarTodos(mixed $paginador): bool
    {
        if (! PanelLayout::seleccionGlobalActiva() || $this->seleccionGlobal) {
            return false;
        }

        if ($paginador instanceof CursorPaginator) {
            return false;
        }

        if ($this->totalRegistros($paginador) <= $paginador->count()) {
            return false;
        }

        return $this->paginaCompletamenteSeleccionada($paginador);
    }

    public function textoRangoResultados(mixed $paginador): string
    {
        return PanelListado::textoRango($paginador);
    }

    public function soloBusquedaActiva(): bool
    {
        return $this->search !== '' && ! $this->tieneFiltrosDeColumnas();
    }

    public function contarFiltrosDeColumnas(): int
    {
        $total = 0;

        foreach ($this->resourceClass::filters() as $filter) {
            if ($this->filtroColumnaTieneValor($filter)) {
                $total++;
            }
        }

        return $total;
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || $this->contarFiltrosDeColumnas() > 0;
    }

    private function tieneFiltrosDeColumnas(): bool
    {
        return $this->contarFiltrosDeColumnas() > 0;
    }

    private function filtroColumnaTieneValor(\MyLaravelTools\Panel\Filters\Filter $filter): bool
    {
        $name = $filter->getName();
        $value = $this->filterValues[$name] ?? null;

        if ($filter->getType() === 'date-range') {
            return ($value['from'] ?? '') !== '' || ($value['to'] ?? '') !== '';
        }

        if ($filter->getType() === 'multi-select') {
            return is_array($value) && $value !== [];
        }

        return $value !== null && $value !== '';
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
            'impersonate' => $this->impersonate($record),
            default => tap(null, function () use ($action, $record): void {
                $action->run($record);
                $this->toastSuccess(__('panel::panel.action_completed'));
            }),
        };
    }

    private function impersonate(Model $record): void
    {
        if (! PanelImpersonation::start($record)) {
            $this->toastError(__('panel::panel.impersonate.failed'));

            return;
        }

        $this->redirect(\panel_route('dashboard', [], false), navigate: false);
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
        $this->seleccionGlobal = false;
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
            $this->askConfirm($this->mensajeConfirmacionBulk($action), 'bulk', bulkAction: $actionName);

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
        $this->limpiarSeleccion();
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

    public function openImportModal(): void
    {
        abort_unless($this->resourceClass::authorize('create'), 403);
        abort_unless((bool) config('panel.import.enabled', true), 403);

        $this->importFile = null;
        $this->importStep = 'upload';
        $this->importPreview = null;
        $this->importResumen = null;
        $this->resetValidation();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importStep = 'upload';
        $this->importPreview = null;
        $this->importResumen = null;
        $this->resetValidation();
    }

    public function backImportUpload(): void
    {
        $this->importStep = 'upload';
        $this->importPreview = null;
        $this->importFile = null;
        $this->resetValidation();
    }

    public function updatedImportFile(): void
    {
        if (! $this->importFile || ! (bool) config('panel.import.preview', true)) {
            return;
        }

        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ], [], [
            'importFile' => __('panel::panel.import.file'),
        ]);

        $extension = strtolower($this->importFile->getClientOriginalExtension());
        $preview = app(ResourceImporter::class)->analyzePath(
            $this->resourceClass,
            $this->importFile->getRealPath(),
            $extension,
        );

        if ($preview['errors'] !== []) {
            $this->addError('importFile', $preview['errors'][0]);
            $this->importPreview = null;
            $this->importStep = 'upload';

            return;
        }

        $this->importPreview = $preview;
        $this->importStep = 'preview';
    }

    public function downloadImportTemplateCsv(): StreamedResponse
    {
        return app(ImportTemplateExporter::class)->downloadCsv($this->resourceClass);
    }

    public function downloadImportTemplateExcel(): StreamedResponse
    {
        return app(ImportTemplateExporter::class)->downloadExcel($this->resourceClass);
    }

    public function processImport(): void
    {
        if ((bool) config('panel.import.preview', true)) {
            $this->confirmImport();

            return;
        }

        $this->importDirect();
    }

    public function confirmImport(): void
    {
        abort_unless($this->resourceClass::authorize('create'), 403);

        if ($this->importPreview === null) {
            $this->importDirect();

            return;
        }

        $payloads = array_values(array_filter(array_map(
            fn (array $row): ?array => $row['valid'] ? $row['payload'] : null,
            $this->importPreview['rows'] ?? [],
        )));

        if ($payloads === []) {
            $this->toastError(__('panel::panel.import.preview_no_valid'));

            return;
        }

        if (PanelLayout::importacionGuiada()) {
            $this->importStep = 'importing';
        }

        $result = app(ResourceImporter::class)->importPayloads($this->resourceClass, $payloads);

        if (PanelLayout::importacionGuiada()) {
            $this->finalizarImportacionGuiada($result);

            return;
        }

        $this->closeImportModal();
        $this->finishImportResult($result);
    }

    private function importDirect(): void
    {
        abort_unless($this->resourceClass::authorize('create'), 403);

        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ], [], [
            'importFile' => __('panel::panel.import.file'),
        ]);

        if (PanelLayout::importacionGuiada()) {
            $this->importStep = 'importing';
        }

        $extension = strtolower($this->importFile->getClientOriginalExtension());
        $result = app(ResourceImporter::class)->fromPath(
            $this->resourceClass,
            $this->importFile->getRealPath(),
            $extension,
        );

        if (PanelLayout::importacionGuiada()) {
            $this->finalizarImportacionGuiada($result);

            return;
        }

        $this->closeImportModal();
        $this->finishImportResult($result);
    }

    /** @param array{imported: int, updated?: int, failed: int, errors: list<string>} $result */
    private function finalizarImportacionGuiada(array $result): void
    {
        $actualizados = (int) ($result['updated'] ?? 0);
        $importados = (int) $result['imported'];
        $fallidos = (int) $result['failed'];

        $this->importResumen = [
            'imported' => $importados,
            'updated' => $actualizados,
            'failed' => $fallidos,
            'errors' => array_slice($result['errors'], 0, 10),
            'ok' => $fallidos === 0 && ($importados > 0 || $actualizados > 0),
        ];
        $this->importStep = 'summary';
        $this->importFile = null;
        $this->importPreview = null;
        $this->resetPage();
    }

    /** @param array{imported: int, updated?: int, failed: int, errors: list<string>} $result */
    private function finishImportResult(array $result): void
    {
        $actualizados = (int) ($result['updated'] ?? 0);

        if ($result['imported'] === 0 && $actualizados === 0 && $result['failed'] === 0 && $result['errors'] !== []) {
            $this->toastError($result['errors'][0]);

            return;
        }

        if ($result['failed'] > 0) {
            $this->toastError(__('panel::panel.import.partial', [
                'imported' => $result['imported'],
                'failed' => $result['failed'],
            ]));

            return;
        }

        if ($actualizados > 0) {
            $this->toastSuccess(__('panel::panel.import.upsert_success', [
                'imported' => $result['imported'],
                'updated' => $actualizados,
            ]));
        } else {
            $this->toastSuccess(__('panel::panel.import.success', ['count' => $result['imported']]));
        }

        $this->resetPage();
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
            'hasActiveFilters' => $this->hasActiveFilters(),
            'cantidadFiltrosActivos' => $this->contarFiltrosDeColumnas(),
            'soloBusquedaActiva' => $this->soloBusquedaActiva(),
            'search' => $this->search,
            'rowActions' => $resourceClass::rowActions(),
            'bulkActions' => $this->visibleBulkActions(),
            'tableClasses' => \MyLaravelTools\Panel\Support\PanelLayout::clasesTabla(),
            'perPageOptions' => $this->perPageOptions(),
            'records' => $records = $this->paginateRecords(),
            'chipsCriterios' => $this->chipsCriteriosActivos(),
            'textoRangoResultados' => $this->textoRangoResultados($records),
            'idsRegistrosEditables' => $this->idsRegistrosEditables($records),
            'filasClicables' => PanelLayout::filasClicables(),
            'tarjetasMovil' => PanelLayout::tarjetasMovil(),
            'columnasOcultables' => PanelLayout::columnasOcultables(),
            'vistaRapida' => PanelLayout::vistaRapida(),
            'presetsFiltros' => PanelLayout::presetsFiltros() && count($resourceClass::filters()) > 0,
            'columnasMeta' => array_map(
                static fn ($columna): array => ['nombre' => $columna->getName(), 'etiqueta' => $columna->getLabel()],
                $resourceClass::table(),
            ),
            'hasBulkActions' => $this->visibleBulkActions() !== [],
            'selectedCount' => $this->contarSeleccion($records),
            'seleccionGlobal' => $this->seleccionGlobal,
            'mostrarSeleccionarTodos' => $this->mostrarSeleccionarTodos($records),
            'totalResultados' => $this->totalRegistros($records),
            'showVistaRapida' => $this->showVistaRapida,
            'vistaRapidaRegistro' => $this->vistaRapidaId !== null
                ? $this->resourceClass::findRecord($this->vistaRapidaId, $this->trashed === 'only')
                : null,
            'formsInModal' => $this->formsInModal(),
            'showFormModal' => $this->showFormModal,
            'formRecordId' => $this->formRecordId,
            'formSchema' => $formSchema,
            'hasTabs' => FormSchema::hasTabs($formSchema),
            'hasSections' => FormSchema::hasSections($formSchema),
            'canImport' => (bool) config('panel.import.enabled', true) && $resourceClass::authorize('create'),
            'importPreviewEnabled' => (bool) config('panel.import.preview', true),
            'showImportModal' => $this->showImportModal,
            'importStep' => $this->importStep,
            'importPreview' => $this->importPreview,
            'importResumen' => $this->importResumen,
            'importacionGuiada' => PanelLayout::importacionGuiada(),
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
            perPage: $this->perPage,
        );
    }

    /** @return list<int> */
    private function perPageOptions(): array
    {
        $opciones = $this->resourceClass::perPageOptions();

        return $opciones !== [] ? $opciones : \MyLaravelTools\Panel\Support\PanelLayout::opcionesPorPagina();
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

    private function restablecerFiltro(\MyLaravelTools\Panel\Filters\Filter $filtro): void
    {
        $nombre = $filtro->getName();

        match ($filtro->getType()) {
            'date-range' => $this->filterValues[$nombre] = ['from' => '', 'to' => ''],
            'multi-select' => $this->filterValues[$nombre] = [],
            default => $this->filterValues[$nombre] = '',
        };
    }

    /** @return array<int|string, true> */
    private function idsRegistrosEditables(mixed $paginador): array
    {
        if (! PanelLayout::filasClicables()) {
            return [];
        }

        $accion = $this->findRowAction('edit');

        if ($accion === null) {
            return [];
        }

        $ids = [];

        foreach ($paginador as $registro) {
            if ($accion->isVisible($registro, $this->resourceClass)) {
                $ids[$registro->getKey()] = true;
            }
        }

        return $ids;
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
        if ($this->seleccionGlobal) {
            $ids = $this->idsRegistrosFiltrados();

            if ($ids === []) {
                return new Collection();
            }

            $consulta = $this->resourceClass::modelClass()::query()->whereIn('id', $ids);

            if ($this->trashed === 'only' && $this->resourceClass::usesSoftDeletes()) {
                $consulta->onlyTrashed();
            }

            return $consulta->get();
        }

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

    private function contarRegistrosFiltrados(): int
    {
        return (new ResourceQuery($this->resourceClass))->contar(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
        );
    }

    /** @return list<int|string> */
    private function idsRegistrosFiltrados(): array
    {
        return (new ResourceQuery($this->resourceClass))->ids(
            columns: $this->resourceClass::table(),
            filters: $this->resourceClass::filters(),
            filterValues: $this->filterValues,
            search: $this->search,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            trashed: $this->trashed,
            limite: PanelLayout::maximoSeleccionGlobal(),
        );
    }

    private function paginaCompletamenteSeleccionada(mixed $paginador): bool
    {
        if ($paginador->count() === 0 || $this->selected === []) {
            return false;
        }

        foreach ($paginador as $registro) {
            if (! in_array($registro->getKey(), $this->selected, false)) {
                return false;
            }
        }

        return true;
    }

    private function mensajeConfirmacionBulk(\MyLaravelTools\Panel\Actions\BulkAction $accion): string
    {
        $mensaje = $accion->getConfirmation() ?? __('panel::panel.confirm_bulk_action');

        if (! PanelLayout::previewBulk()) {
            return $mensaje;
        }

        return __('panel::panel.confirm_bulk_preview', [
            'count' => $this->contarSeleccion($this->paginateRecords()),
            'action' => $accion->getLabel(),
        ]);
    }

    private function totalRegistros(mixed $paginador): int
    {
        if ($paginador instanceof CursorPaginator) {
            return $this->contarRegistrosFiltrados();
        }

        return (int) $paginador->total();
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
