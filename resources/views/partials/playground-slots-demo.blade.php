@props(['ubicacion' => 'sidebar.before'])

@php
    $slots = [
        'sidebar.before' => __('panel::panel.documentation.slot_sidebar_before'),
        'sidebar.after' => __('panel::panel.documentation.slot_sidebar_after'),
        'main.before' => __('panel::panel.documentation.slot_main_before'),
        'main.after' => __('panel::panel.documentation.slot_main_after'),
        'topbar.end' => __('panel::panel.documentation.slot_topbar_end'),
    ];
    $etiqueta = $slots[$ubicacion] ?? null;
@endphp

@if ($etiqueta)
    <div class="panel-playground-slot panel-playground-slot--{{ str_replace('.', '-', $ubicacion) }}" data-playground-slot="{{ $ubicacion }}">
        <span class="panel-playground-slot-label">{{ $etiqueta }}</span>
        <code class="panel-playground-slot-code">{{ $ubicacion }}</code>
    </div>
@endif
