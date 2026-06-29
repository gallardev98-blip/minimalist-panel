<div
    class="panel-relation-section"
    x-data="{
        filaActiva: null,
        idsEditables: @js(array_map('strval', array_keys($idsRegistrosEditables ?? []))),
        activarFila(id, elemento) {
            this.filaActiva = String(id);
            elemento?.focus({ preventScroll: true });
        },
        manejarTeclaTabla(e) {
            const etiqueta = document.activeElement?.tagName ?? '';
            const editable = document.activeElement?.isContentEditable;
            const enDialogo = document.activeElement?.closest('[role=dialog]');
            if (enDialogo || ['INPUT', 'TEXTAREA', 'SELECT'].includes(etiqueta) || editable) {
                return;
            }
            const filas = [...(this.$refs.tablaRelacion?.querySelectorAll('[data-fila-id]') ?? [])];
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
        }
    }"
    @keydown.window="manejarTeclaTabla($event)"
>
    @include('panel::partials.confirm-modal', [
        'showConfirm' => $showConfirm ?? false,
        'confirmMessage' => $confirmMessage ?? '',
    ])

    <div class="panel-relation-header">
        <div>
            <h2 class="panel-relation-title">{{ $title }}</h2>
            <p class="panel-index-meta panel-relation-meta">{{ $textoRangoResultados ?? '' }}</p>
        </div>
        @if ($canCreate && ! $showForm)
            <button type="button" wire:click="openCreateForm" class="panel-btn panel-btn-primary panel-btn-compact">
                <x-panel::icon name="plus" class="h-4 w-4" />
                {{ __('panel::panel.add') }}
            </button>
        @endif
    </div>

    @if ($showForm)
        <form wire:submit="save" class="panel-form-card panel-relation-form">
            <div class="panel-form-card-body">
                @include('panel::partials.form-schema', ['formSchema' => $formSchema])
            </div>
            <div class="panel-form-footer">
                <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('panel::panel.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('panel::panel.loading') }}</span>
                </button>
                <button type="button" wire:click="cancelForm" class="panel-btn panel-btn-ghost">
                    {{ __('panel::panel.cancel') }}
                </button>
            </div>
        </form>
    @endif

    @php
        use MyLaravelTools\Panel\Support\PanelListado;
        use MyLaravelTools\Panel\Support\PanelRendimiento;

        $objetivosCarga = PanelListado::objetivosCargaRelacion();
        $retardoSkeleton = PanelRendimiento::retardoSkeletonMs();
        $retardoOcultarTabla = PanelRendimiento::retardoOcultarTablaMs();
    @endphp

    <div class="panel-relation-table">
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
            @include('panel::partials.skeleton-table', [
                'columns' => $columns,
                'hasBulkActions' => false,
                'tableClasses' => $tableClasses ?? '',
                'perPage' => 5,
                'tienePerPage' => false,
                'tienePaginacion' => $records->hasPages(),
            ])
        </div>

        <div
            class="panel-table-wrap {{ $tableClasses ?? '' }}"
            wire:loading.class="panel-table-wrap--busy"
            @if ($retardoOcultarTabla > 0)
                wire:loading.delay.{{ $retardoOcultarTabla }}ms.class="hidden"
            @else
                wire:loading.class="hidden"
            @endif
            wire:target="{{ $objetivosCarga }}"
        >
            <div class="panel-table-scroll">
                <table class="panel-table {{ $tableClasses ?? '' }}">
                    <thead>
                        <tr>
                            @foreach ($columns as $column)
                                <th>{{ $column->getLabel() }}</th>
                            @endforeach
                            <th class="panel-table-actions-col">
                                <span class="sr-only">{{ __('panel::panel.actions') }}</span>
                                <x-panel::icon name="settings" class="panel-muted mx-auto h-4 w-4" aria-hidden="true" />
                            </th>
                        </tr>
                    </thead>
                    <tbody x-ref="tablaRelacion">
                        @forelse ($records as $record)
                            @php
                                $puedeAbrir = ($filasClicables ?? false) && isset($idsRegistrosEditables[$record->getKey()]);
                            @endphp
                            <tr
                                wire:key="rel-{{ $record->getKey() }}"
                                data-fila-id="{{ $record->getKey() }}"
                                @class(['panel-table-row--clickable' => $puedeAbrir])
                                x-bind:class="{ 'panel-table-row--active': filaActiva == '{{ $record->getKey() }}' }"
                                x-bind:tabindex="filaActiva == '{{ $record->getKey() }}' ? 0 : -1"
                                @if ($puedeAbrir)
                                    wire:click="abrirRegistro({{ $record->getKey() }})"
                                    role="link"
                                    wire:keydown.enter="abrirRegistro({{ $record->getKey() }})"
                                @endif
                            >
                                @foreach ($columns as $column)
                                    <td>
                                        @include('panel::partials.column-value', ['column' => $column, 'record' => $record])
                                    </td>
                                @endforeach
                                <td class="panel-table-actions-col" wire:click.stop>
                                    @include('panel::partials.relation-row-actions', [
                                        'record' => $record,
                                        'canUpdate' => $childResourceClass::authorize('update', $record),
                                        'canDelete' => $childResourceClass::authorize('delete', $record),
                                        'isPivotRelation' => $isPivotRelation ?? false,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="py-8 text-center">
                                    <p class="panel-muted text-sm">{{ __('panel::panel.related_empty') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($records->hasPages())
                <div class="panel-table-footer">
                    <div class="panel-table-footer__left"></div>
                    <div class="panel-table-footer__right">
                        {{ $records->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
