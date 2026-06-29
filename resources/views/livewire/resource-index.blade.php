<div
    @class(['panel-index-root', 'panel-index-root--bulk' => $selectedCount > 0 && $hasBulkActions])
    x-data="{
        filaActiva: null,
        idsEditables: @js(array_map('strval', array_keys($idsRegistrosEditables ?? []))),
        enfocarBusqueda() {
            const campo = this.$refs.busquedaIndex ?? document.querySelector('[data-panel-index-search]');
            campo?.focus();
        },
        activarFila(id, elemento) {
            this.filaActiva = String(id);
            elemento?.focus({ preventScroll: true });
        },
        manejarTeclaGlobal(e) {
            const etiqueta = document.activeElement?.tagName ?? '';
            const editable = document.activeElement?.isContentEditable;
            const enDialogo = document.activeElement?.closest('[role=dialog]');
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                this.enfocarBusqueda();
                return;
            }
            if (e.key === '/' && ! ['INPUT', 'TEXTAREA', 'SELECT'].includes(etiqueta) && ! editable) {
                e.preventDefault();
                this.enfocarBusqueda();
                return;
            }
            if (enDialogo || ['INPUT', 'TEXTAREA', 'SELECT'].includes(etiqueta) || editable) {
                return;
            }
            const filas = [...(this.$refs.tablaCuerpo?.querySelectorAll('[data-fila-id]') ?? [])];
            if (filas.length === 0) {
                return;
            }
            const ids = filas.map((fila) => fila.dataset.filaId ?? '');
            let indice = this.filaActiva ? ids.indexOf(this.filaActiva) : -1;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                indice = Math.min(indice + 1, filas.length - 1);
                this.activarFila(ids[indice], filas[indice]);
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                indice = indice < 0 ? 0 : Math.max(indice - 1, 0);
                this.activarFila(ids[indice], filas[indice]);
                return;
            }
            if (e.key === 'Enter' && this.filaActiva && this.idsEditables.includes(this.filaActiva)) {
                e.preventDefault();
                $wire.abrirRegistro(this.filaActiva);
            }
        },
        manejarClicFila(evento, id, puedeAbrir, vistaRapida) {
            if (evento.shiftKey && vistaRapida) {
                evento.preventDefault();
                evento.stopPropagation();
                $wire.abrirVistaRapida(id);
                return;
            }
            if (! puedeAbrir) {
                return;
            }
            if (evento.target.closest('input,button,a,[data-panel-row-actions]')) {
                return;
            }
            $wire.abrirRegistro(id);
        }
    }"
    @keydown.window="manejarTeclaGlobal($event)"
>
    @include('panel::partials.confirm-modal', [
        'showConfirm' => $showConfirm,
        'confirmMessage' => $confirmMessage,
    ])

    @if ($formsInModal ?? false)
        @include('panel::partials.form-modal', [
            'showFormModal' => $showFormModal,
            'formRecordId' => $formRecordId,
            'resourceLabel' => $resourceLabel,
            'resourceSlug' => $resourceSlug,
            'formSchema' => $formSchema,
            'hasTabs' => $hasTabs,
        ])
    @endif

    @include('panel::partials.import-modal', [
        'showImportModal' => $showImportModal ?? false,
        'resourceLabel' => $resourceLabel,
    ])

    <x-panel::page-header class="mb-4">
        <h1>{{ $resourceLabel }}</h1>
        <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.records_list') }}</p>
    </x-panel::page-header>

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-end">
        <div class="panel-toolbar">
            <div class="panel-toolbar-group">
                @if ($usesSoftDeletes)
                    <x-panel::searchable-select
                        wire:model.live="trashed"
                        :options="[
                            '' => __('panel::panel.active'),
                            'only' => __('panel::panel.trashed'),
                            'with' => __('panel::panel.all'),
                        ]"
                        class="panel-input-inline text-sm"
                    />
                @endif

                @if ($canCreate && $trashed !== 'only')
                    @if ($formsInModal ?? false)
                        <button type="button" wire:click="openCreateFormModal" class="panel-btn panel-btn-primary panel-btn-compact">
                            <x-panel::icon name="plus" class="h-4 w-4" />
                            {{ __('panel::panel.create') }}
                        </button>
                    @else
                        <a
                            href="{{ panel_route('resources.create', ['resource' => $resourceSlug]) }}"
                            class="panel-btn panel-btn-primary panel-btn-compact"
                            wire:navigate wire:navigate.hover
                        >
                            <x-panel::icon name="plus" class="h-4 w-4" />
                            {{ __('panel::panel.create') }}
                        </a>
                    @endif
                @endif

                @if (($canImport ?? false) && $trashed !== 'only')
                    <button
                        type="button"
                        wire:click="openImportModal"
                        class="panel-btn panel-btn-secondary panel-btn-compact"
                        title="{{ __('panel::panel.import.action') }}"
                    >
                        <x-panel::icon name="upload" class="h-4 w-4" />
                        {{ __('panel::panel.import.short') }}
                    </button>
                @endif

                <div class="panel-export-dropdown" role="group" aria-label="{{ __('panel::panel.export_group') }}">
                    @include('panel::partials.export-dropdown', ['selectedCount' => $selectedCount])
                </div>
            </div>
        </div>
    </div>

    @include('panel::partials.index-listado-scripts')

    @if (count($filters) > 0)
        @include('panel::partials.filters', [
            'filters' => $filters,
            'hasActiveFilters' => $hasActiveFilters,
            'cantidadFiltrosActivos' => $cantidadFiltrosActivos ?? 0,
            'search' => $search,
            'chipsCriterios' => $chipsCriterios ?? [],
            'textoRangoResultados' => $textoRangoResultados ?? '',
            'presetsFiltros' => $presetsFiltros ?? false,
            'resourceSlug' => $resourceSlug,
        ])
    @else
        <div class="panel-index-tools mb-4">
            <div class="panel-index-tools__search-row">
                <div class="panel-index-tools__search">
                    @include('panel::partials.index-search', ['search' => $search])
                </div>
                <div class="panel-index-meta-row">
                    <p
                        class="panel-index-meta"
                        wire:loading.class="panel-index-meta--loading"
                        wire:target="search,filterValues,resetFilters,quitarCriterio,trashed,sortBy,gotoPage,nextPage,previousPage,perPage"
                    >
                        {{ $textoRangoResultados ?? '' }}
                    </p>
                    @include('panel::partials.copy-list-link')
                </div>
            </div>
            @include('panel::partials.active-filter-chips', ['chipsCriterios' => $chipsCriterios ?? []])
        </div>
    @endif

    @php
        use MyLaravelTools\Panel\Support\PanelListado;
        use MyLaravelTools\Panel\Support\PanelRendimiento;

        $retardoSkeleton = PanelRendimiento::retardoSkeletonMs();
        $retardoOcultarTabla = PanelRendimiento::retardoOcultarTablaMs();
        $objetivosCarga = PanelListado::objetivosCarga();
    @endphp

    <div class="panel-index-table" x-data="panelColumnas(@js($columnasMeta ?? []), @js($resourceSlug))">
        <div class="panel-index-table__tools panel-column-toggle">
            @include('panel::partials.column-toggle', [
                'columnasOcultables' => $columnasOcultables ?? false,
                'columnasMeta' => $columnasMeta ?? [],
                'resourceSlug' => $resourceSlug,
            ])
        </div>
        <div
            @if ($retardoSkeleton > 0)
                wire:loading.delay.{{ $retardoSkeleton }}ms.class.remove="hidden"
            @else
                wire:loading.class.remove="hidden"
            @endif
            wire:target="{{ $objetivosCarga }}"
            class="hidden"
            aria-hidden="true"
        >
            <div @class(['panel-only-desktop' => $tarjetasMovil ?? false])>
                @include('panel::partials.skeleton-table', [
                    'columns' => $columns,
                    'hasBulkActions' => $hasBulkActions,
                    'tableClasses' => $tableClasses ?? '',
                    'perPage' => $perPage,
                    'tienePerPage' => count($perPageOptions ?? []) > 1,
                    'tienePaginacion' => $records->hasPages(),
                ])
            </div>
            @if ($tarjetasMovil ?? false)
                @include('panel::partials.skeleton-cards', ['perPage' => $perPage])
            @endif
        </div>

        @if ($tarjetasMovil ?? false)
            <div
                class="panel-record-cards-wrap panel-only-mobile"
                wire:loading.class="panel-record-cards-wrap--busy"
                @if ($retardoOcultarTabla > 0)
                    wire:loading.delay.{{ $retardoOcultarTabla }}ms.class="hidden"
                @else
                    wire:loading.class="hidden"
                @endif
                wire:target="{{ $objetivosCarga }}"
            >
                <div class="panel-record-cards">
                    @forelse ($records as $record)
                        @include('panel::partials.record-card', [
                            'record' => $record,
                            'columns' => $columns,
                            'rowActions' => $rowActions,
                            'resourceClass' => $resourceClass,
                            'resourceSlug' => $resourceSlug,
                            'formsInModal' => $formsInModal ?? false,
                            'usesSoftDeletes' => $usesSoftDeletes,
                            'filasClicables' => $filasClicables ?? false,
                            'idsRegistrosEditables' => $idsRegistrosEditables ?? [],
                        ])
                    @empty
                        <div class="panel-record-cards-empty">
                            @include('panel::partials.empty-table', [
                                'trashed' => $trashed,
                                'hasActiveFilters' => $hasActiveFilters,
                                'soloBusquedaActiva' => $soloBusquedaActiva,
                                'search' => $search,
                                'canCreate' => $canCreate,
                                'formsInModal' => $formsInModal ?? false,
                                'resourceLabel' => $resourceLabel,
                                'resourceSlug' => $resourceSlug,
                            ])
                        </div>
                    @endforelse
                </div>

                @include('panel::partials.table-footer', [
                    'paginator' => $records,
                    'perPageOptions' => $perPageOptions ?? [],
                ])
            </div>
        @endif

        <div
            @class([
                'panel-table-wrap',
                $tableClasses ?? null,
                'panel-only-desktop' => $tarjetasMovil ?? false,
            ])
            wire:loading.class="panel-table-wrap--busy"
            @if ($retardoOcultarTabla > 0)
                wire:loading.delay.{{ $retardoOcultarTabla }}ms.class="hidden"
            @else
                wire:loading.class="hidden"
            @endif
            wire:target="{{ $objetivosCarga }}"
        >
        <div class="panel-table-scroll overflow-x-auto">
            <table class="panel-table {{ $tableClasses ?? '' }}">
                <thead>
                    <tr>
                        @if ($hasBulkActions)
                            <th class="w-10">
                                <input type="checkbox" class="panel-checkbox" wire:click="toggleSelectAll">
                            </th>
                        @endif
                        @foreach ($columns as $column)
                            <th x-show="esVisible(@js($column->getName()))" x-cloak>
                                @if ($column->isSortable())
                                    <button type="button" wire:click="sortBy('{{ $column->getName() }}')" class="panel-table-sort inline-flex items-center gap-1">
                                        {{ $column->getLabel() }}
                                        @if ($sortColumn === $column->getName())
                                            <x-panel::icon :name="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" class="h-3.5 w-3.5" />
                                        @endif
                                    </button>
                                @else
                                    {{ $column->getLabel() }}
                                @endif
                            </th>
                        @endforeach
                        <th class="panel-table-actions-col">
                            <span class="sr-only">{{ __('panel::panel.actions') }}</span>
                            <x-panel::icon name="settings" class="panel-muted mx-auto h-4 w-4" aria-hidden="true" />
                        </th>
                    </tr>
                </thead>
                <tbody x-ref="tablaCuerpo">
                    @forelse ($records as $record)
                        @php
                            $puedeAbrir = ($filasClicables ?? false) && isset($idsRegistrosEditables[$record->getKey()]);
                        @endphp
                        <tr
                            @class([
                                'panel-table-row--clickable' => $puedeAbrir,
                                'opacity-60' => $usesSoftDeletes && method_exists($record, 'trashed') && $record->trashed(),
                            ])
                            data-fila-id="{{ $record->getKey() }}"
                            wire:key="record-{{ $record->getKey() }}"
                            x-bind:class="{ 'panel-table-row--active': filaActiva == '{{ $record->getKey() }}' }"
                            x-bind:tabindex="filaActiva == '{{ $record->getKey() }}' ? 0 : -1"
                            @if ($puedeAbrir || ($vistaRapida ?? false))
                                @click="manejarClicFila($event, '{{ $record->getKey() }}', @js($puedeAbrir), @js($vistaRapida ?? false))"
                                role="link"
                                wire:keydown.enter="abrirRegistro({{ $record->getKey() }})"
                            @endif
                        >
                            @if ($hasBulkActions)
                                <td wire:click.stop>
                                    <input
                                        type="checkbox"
                                        value="{{ $record->getKey() }}"
                                        wire:model.live="selected"
                                        class="panel-checkbox"
                                    >
                                </td>
                            @endif
                            @foreach ($columns as $column)
                                <td x-show="esVisible(@js($column->getName()))" x-cloak>
                                    @include('panel::partials.column-value', ['column' => $column, 'record' => $record])
                                </td>
                            @endforeach
                            <td class="panel-table-actions-col" wire:click.stop data-panel-row-actions>
                                @if ($vistaRapida ?? false)
                                    <button
                                        type="button"
                                        wire:click.stop="abrirVistaRapida({{ $record->getKey() }})"
                                        class="panel-quick-view-btn"
                                        title="{{ __('panel::panel.quick_view') }}"
                                        aria-label="{{ __('panel::panel.quick_view') }}"
                                    >
                                        <x-panel::icon name="eye" class="h-4 w-4" />
                                    </button>
                                @endif
                                @include('panel::partials.row-actions', [
                                    'rowActions' => $rowActions,
                                    'record' => $record,
                                    'resourceClass' => $resourceClass,
                                    'resourceSlug' => $resourceSlug,
                                    'formsInModal' => $formsInModal ?? false,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + ($hasBulkActions ? 2 : 1) }}" class="p-0">
                                @include('panel::partials.empty-table', [
                                    'trashed' => $trashed,
                                    'hasActiveFilters' => $hasActiveFilters,
                                    'soloBusquedaActiva' => $soloBusquedaActiva,
                                    'search' => $search,
                                    'canCreate' => $canCreate,
                                    'formsInModal' => $formsInModal ?? false,
                                    'resourceLabel' => $resourceLabel,
                                    'resourceSlug' => $resourceSlug,
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('panel::partials.table-footer', [
            'paginator' => $records,
            'perPageOptions' => $perPageOptions ?? [],
        ])
        </div>
    </div>

    @include('panel::partials.quick-view-drawer', [
        'showVistaRapida' => $showVistaRapida ?? false,
        'vistaRapidaRegistro' => $vistaRapidaRegistro ?? null,
        'columns' => $columns,
        'resourceSlug' => $resourceSlug,
        'formsInModal' => $formsInModal ?? false,
        'idsRegistrosEditables' => $idsRegistrosEditables ?? [],
    ])

    @include('panel::partials.bulk-bar', [
        'selectedCount' => $selectedCount,
        'hasBulkActions' => $hasBulkActions,
        'bulkActions' => $bulkActions,
        'mostrarSeleccionarTodos' => $mostrarSeleccionarTodos ?? false,
        'seleccionGlobal' => $seleccionGlobal ?? false,
        'totalResultados' => $totalResultados ?? 0,
    ])
</div>
