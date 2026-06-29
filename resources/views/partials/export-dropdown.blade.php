@php
    $exportHint = $selectedCount > 0
        ? __('panel::panel.export_selection_hint', ['count' => $selectedCount])
        : __('panel::panel.export_all_hint');
@endphp

<div
    class="panel-export-dropdown"
    x-data="{ abierto: false }"
    @keydown.escape.window="abierto = false"
>
    <button
        type="button"
        class="panel-btn panel-btn-secondary panel-btn-compact panel-export-dropdown__toggle"
        @click="abierto = ! abierto"
        x-bind:aria-expanded="abierto"
        aria-haspopup="menu"
        title="{{ $exportHint }}"
    >
        <x-panel::icon name="download" class="h-4 w-4" />
        {{ __('panel::panel.export_group') }}
        <x-panel::icon name="chevron-down" class="panel-export-dropdown__chevron h-3.5 w-3.5" x-bind:class="abierto ? 'panel-export-dropdown__chevron--open' : ''" />
    </button>

    <div
        x-show="abierto"
        x-cloak
        x-transition.opacity.duration.150ms
        @click.outside="abierto = false"
        class="panel-export-dropdown__menu"
        role="menu"
    >
        <button
            type="button"
            wire:click="exportCsv"
            @click="abierto = false"
            class="panel-export-dropdown__item"
            role="menuitem"
            title="{{ __('panel::panel.export_csv') }} — {{ $exportHint }}"
        >
            <x-panel::icon name="file" class="h-4 w-4 shrink-0" />
            <span>{{ __('panel::panel.export_csv') }}</span>
        </button>
        <button
            type="button"
            wire:click="exportExcel"
            @click="abierto = false"
            class="panel-export-dropdown__item"
            role="menuitem"
            title="{{ __('panel::panel.export_excel') }} — {{ $exportHint }}"
        >
            <x-panel::icon name="file-spreadsheet" class="h-4 w-4 shrink-0" />
            <span>{{ __('panel::panel.export_excel') }}</span>
        </button>
        <button
            type="button"
            wire:click="exportPdf"
            @click="abierto = false"
            class="panel-export-dropdown__item"
            role="menuitem"
            title="{{ __('panel::panel.export_pdf') }} — {{ $exportHint }}"
        >
            <x-panel::icon name="file-text" class="h-4 w-4 shrink-0" />
            <span>{{ __('panel::panel.export_pdf') }}</span>
        </button>
    </div>
</div>
