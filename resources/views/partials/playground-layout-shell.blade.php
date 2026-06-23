@php
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $modo = $modo ?? 'sidebar';
    $etiqueta = PlaygroundDemo::etiquetaModo($modo);
    $bloques = [
        'sidebar' => in_array($modo, ['sidebar', 'dual'], true),
        'topbar' => in_array($modo, ['topbar', 'dual'], true),
        'main' => true,
    ];
@endphp

<div class="panel-playground-layout-shell" role="img" aria-label="{{ __('panel::panel.documentation.layout_shell_aria', ['mode' => $etiqueta]) }}">
    <span class="panel-playground-layout-shell-label">{{ $etiqueta }}</span>
    <div class="panel-playground-layout-shell-grid panel-playground-layout-shell-grid--{{ $modo }}">
        @foreach ($bloques as $id => $activo)
            <span @class([
                'panel-playground-layout-shell-block',
                'panel-playground-layout-shell-block--'.$id,
                'panel-playground-layout-shell-block--active' => $activo,
            ])>
                {{ __("panel::panel.documentation.layout_shell_{$id}") }}
            </span>
        @endforeach
    </div>
</div>
