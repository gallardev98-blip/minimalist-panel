@props([
    'id' => null,
    'options' => [],
    'placeholder' => null,
    'multiple' => false,
    'disabled' => false,
    'teleport' => false,
])

@php
    use MyLaravelTools\Panel\Support\PanelRendimiento;

    $opcionesLista = collect($options)->map(
        fn (string $etiqueta, string|int $valor): array => ['valor' => (string) $valor, 'etiqueta' => $etiqueta]
    )->values()->all();
    $placeholderTexto = $placeholder
        ?? (array_key_exists('', $options) ? (string) $options[''] : __('panel::panel.select'));
    $wireModel = $attributes->wire('model');
    $clases = trim('panel-searchable-select ' . ($attributes->get('class') ?? ''));
    $duracionCierre = PanelRendimiento::cierreSelectMs();
@endphp

<div
    wire:ignore
    class="{{ $clases }}"
    x-data="panelSearchableSelect({
        opciones: @js($opcionesLista),
        multiple: @js($multiple),
        deshabilitado: @js($disabled),
        teleport: @js($teleport),
        placeholder: @js($placeholderTexto),
        textoSeleccionados: @js(__('panel::panel.selected', ['count' => ':count'])),
        sinResultados: @js(__('panel::panel.no_options_found')),
        duracionCierre: @js($duracionCierre),
    })"
    x-init="valor = @entangle($wireModel); initSelect()"
    x-bind:class="{
        'panel-searchable-select--open': abierto,
        'panel-searchable-select--cerrando': cerrando,
    }"
    @keydown="manejarTecla($event)"
    {{ $attributes->except(['class', 'wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer', 'disabled', 'id', 'placeholder', 'multiple', 'options', 'teleport']) }}
>
    <button
        type="button"
        x-ref="trigger"
        @if ($id) id="{{ $id }}" @endif
        class="panel-searchable-select__trigger"
        x-bind:aria-expanded="abierto"
        aria-haspopup="listbox"
        x-bind:disabled="deshabilitado"
        @click="alternar()"
    >
        <span class="panel-searchable-select__value" x-text="textoSeleccionado()"></span>
        <x-panel::icon name="chevron-down" class="panel-searchable-select__chevron h-4 w-4 shrink-0" />
    </button>

    @if ($teleport)
        <template x-teleport="body">
            @include('panel::partials.searchable-select-dropdown')
        </template>
    @else
        @include('panel::partials.searchable-select-dropdown')
    @endif
</div>
