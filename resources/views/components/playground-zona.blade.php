@props([
    'zona',
    'zonasModificadas' => [],
    'zonaResaltada' => null,
])

@php
    $modificada = in_array($zona, $zonasModificadas, true);
    $activa = $zonaResaltada === $zona;
@endphp

<div
    {{ $attributes->class([
        'panel-playground-zona',
        'panel-playground-zona--modificada' => $modificada,
        'panel-playground-zona--activa' => $activa,
    ]) }}
    data-playground-zona="{{ $zona }}"
>
    @if ($modificada || $activa)
        <span class="panel-playground-zona-badge">
            {{ $activa ? __('panel::panel.documentation.changed_here_now') : __('panel::panel.documentation.changed_here') }}
        </span>
    @endif
    {{ $slot }}
</div>
