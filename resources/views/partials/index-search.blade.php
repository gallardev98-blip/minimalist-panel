@php
    use MyLaravelTools\Panel\Support\PanelRendimiento;

    $debounceBusqueda = PanelRendimiento::debounceBusquedaMs();
@endphp

<div @class(['panel-search', $class ?? null])>
    <x-panel::icon name="search" class="panel-search-icon" />
    <input
        type="search"
        @if ($debounceBusqueda > 0)
            wire:model.live.debounce.{{ $debounceBusqueda }}ms="search"
        @else
            wire:model.live="search"
        @endif
        placeholder="{{ __('panel::panel.search') }}"
        class="panel-input panel-search-input {{ ($search ?? '') !== '' ? 'panel-search-input--clearable' : '' }}"
        aria-label="{{ __('panel::panel.search') }}"
        @if ($atajoTeclado ?? true)
            x-ref="busquedaIndex"
            data-panel-index-search
        @endif
    >
    @if (($search ?? '') !== '')
        <button
            type="button"
            wire:click="$set('search', '')"
            class="panel-search-clear"
            aria-label="{{ __('panel::panel.clear_search') }}"
        >
            <x-panel::icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
