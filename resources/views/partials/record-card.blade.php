@php
    $puedeAbrir = ($filasClicables ?? false) && isset($idsRegistrosEditables[$record->getKey()]);
@endphp

<article
    @class([
        'panel-record-card',
        'panel-record-card--clickable' => $puedeAbrir,
        'opacity-60' => ($usesSoftDeletes ?? false) && method_exists($record, 'trashed') && $record->trashed(),
    ])
    wire:key="card-{{ $record->getKey() }}"
    @if ($puedeAbrir)
        wire:click="abrirRegistro({{ $record->getKey() }})"
        tabindex="0"
        role="link"
        wire:keydown.enter="abrirRegistro({{ $record->getKey() }})"
        aria-label="{{ __('panel::panel.open_record') }}"
    @endif
>
    <div class="panel-record-card__body">
        @foreach ($columns as $column)
            <div class="panel-record-card__field">
                <span class="panel-record-card__label">{{ $column->getLabel() }}</span>
                <span class="panel-record-card__value">
                    @include('panel::partials.column-value', ['column' => $column, 'record' => $record])
                </span>
            </div>
        @endforeach
    </div>

    <div class="panel-record-card__actions" wire:click.stop>
        @include('panel::partials.row-actions', [
            'rowActions' => $rowActions,
            'record' => $record,
            'resourceClass' => $resourceClass,
            'resourceSlug' => $resourceSlug,
            'formsInModal' => $formsInModal ?? false,
        ])
    </div>
</article>
