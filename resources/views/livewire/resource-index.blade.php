<div>
    @include('panel::partials.confirm-modal', [
        'showConfirm' => $showConfirm,
        'confirmMessage' => $confirmMessage,
    ])

    @if ($formsInModal ?? false)
        @include('panel::partials.form-modal', [
            'showFormModal' => $showFormModal,
            'formRecordId' => $formRecordId,
            'resourceLabel' => $resourceLabel,
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
            @if ($selectedCount > 0)
                <div class="panel-toolbar-group">
                    <span class="panel-toolbar-selection">{{ __('panel::panel.selected', ['count' => $selectedCount]) }}</span>
                    @foreach ($bulkActions as $action)
                        @php
                            $btnClass = match ($action->getColor()) {
                                'rose' => 'panel-btn-danger',
                                'emerald' => 'panel-btn-success',
                                default => 'panel-btn-secondary',
                            };
                        @endphp
                        <button
                            type="button"
                            wire:click="runBulkAction('{{ $action->getName() }}')"
                            class="panel-btn {{ $btnClass }} panel-btn-compact"
                        >
                            {{ $action->getLabel() }}
                        </button>
                    @endforeach
                </div>
                <span class="panel-toolbar-divider" aria-hidden="true"></span>
            @endif

            <div class="panel-toolbar-group">
                @if ($usesSoftDeletes)
                    <select wire:model.live="trashed" class="panel-input panel-input-inline text-sm">
                        <option value="">{{ __('panel::panel.active') }}</option>
                        <option value="only">{{ __('panel::panel.trashed') }}</option>
                        <option value="with">{{ __('panel::panel.all') }}</option>
                    </select>
                @endif

                @if ($canCreate && $trashed !== 'only')
                    @if ($formsInModal ?? false)
                        <button type="button" wire:click="openCreateFormModal" class="panel-btn panel-btn-primary panel-btn-compact">
                            <x-panel::icon name="plus" class="h-4 w-4" />
                            {{ __('panel::panel.create') }}
                        </button>
                    @else
                        <a
                            href="{{ route('panel.resources.create', ['resource' => $resourceSlug]) }}"
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

                <div class="panel-export-group" role="group" aria-label="{{ __('panel::panel.export_group') }}">
                    @php
                        $exportHint = $selectedCount > 0
                            ? __('panel::panel.export_selection_hint', ['count' => $selectedCount])
                            : __('panel::panel.export_all_hint');
                    @endphp
                    <button
                        type="button"
                        wire:click="exportCsv"
                        class="panel-btn panel-btn-secondary panel-btn-compact panel-export-btn"
                        title="{{ __('panel::panel.export_csv_short') }} — {{ $exportHint }}"
                    >
                        {{ __('panel::panel.export_csv_short') }}
                    </button>
                    <button
                        type="button"
                        wire:click="exportExcel"
                        class="panel-btn panel-btn-secondary panel-btn-compact panel-export-btn"
                        title="{{ __('panel::panel.export_excel_short') }} — {{ $exportHint }}"
                    >
                        {{ __('panel::panel.export_excel_short') }}
                    </button>
                    <button
                        type="button"
                        wire:click="exportPdf"
                        class="panel-btn panel-btn-secondary panel-btn-compact panel-export-btn"
                        title="{{ __('panel::panel.export_pdf_short') }} — {{ $exportHint }}"
                    >
                        {{ __('panel::panel.export_pdf_short') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="panel-search">
            <x-panel::icon name="search" class="panel-search-icon" />
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('panel::panel.search') }}"
                class="panel-input panel-search-input"
            >
        </div>

        @include('panel::partials.filters', ['filters' => $filters])
    </div>

    <div wire:loading.delay.class.remove="hidden" wire:target="search,filterValues,resetFilters,trashed,sortBy,gotoPage,nextPage,previousPage" class="hidden">
        @include('panel::partials.skeleton-table')
    </div>

    <div class="panel-table-wrap {{ $tableClasses ?? '' }}" wire:loading.delay.class="opacity-50" wire:target="search,filterValues,resetFilters,trashed,sortBy,gotoPage,nextPage,previousPage,perPage">
        <div class="overflow-x-auto">
            <table class="panel-table {{ $tableClasses ?? '' }}">
                <thead>
                    <tr>
                        @if ($hasBulkActions)
                            <th class="w-10">
                                <input type="checkbox" class="panel-checkbox" wire:click="toggleSelectAll">
                            </th>
                        @endif
                        @foreach ($columns as $column)
                            <th>
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
                <tbody>
                    @forelse ($records as $record)
                        <tr class="{{ $usesSoftDeletes && method_exists($record, 'trashed') && $record->trashed() ? 'opacity-60' : '' }}" wire:key="record-{{ $record->getKey() }}">
                            @if ($hasBulkActions)
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $record->getKey() }}"
                                        wire:model.live="selected"
                                        class="panel-checkbox"
                                    >
                                </td>
                            @endif
                            @foreach ($columns as $column)
                                <td>
                                    @include('panel::partials.column-value', ['column' => $column, 'record' => $record])
                                </td>
                            @endforeach
                            <td class="panel-table-actions-col">
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
                            <td colspan="{{ count($columns) + ($hasBulkActions ? 2 : 1) }}" class="py-12 text-center">
                                <x-panel::icon name="archive" class="panel-muted mx-auto mb-2 h-8 w-8" />
                                <p class="panel-muted text-sm">{{ __('panel::panel.no_records') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages() || count($perPageOptions ?? []) > 1)
            <div class="panel-border flex flex-wrap items-center justify-between gap-3 border-t px-4 py-3">
                @if (count($perPageOptions ?? []) > 1)
                    <label class="panel-muted flex items-center gap-2 text-sm">
                        <span>{{ __('panel::panel.per_page') }}</span>
                        <select wire:model.live="perPage" class="panel-input !w-auto !py-1 text-sm">
                            @foreach ($perPageOptions as $opcion)
                                <option value="{{ $opcion }}">{{ $opcion }}</option>
                            @endforeach
                        </select>
                    </label>
                @else
                    <span></span>
                @endif
                @if ($records->hasPages())
                    {{ $records->links() }}
                @endif
            </div>
        @endif
    </div>
</div>
