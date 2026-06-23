@php
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $widget = PlaygroundDemo::widgetVista();
@endphp

<x-panel::playground-zona zona="contenido" :$zonasModificadas :$zonaResaltada class="mb-8">
    <div class="panel-card p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.view_widget_title') }}</h2>
                <p class="panel-muted mt-1 text-xs">{{ __('panel::panel.documentation.view_widget_desc') }}</p>
            </div>
            <span class="panel-badge panel-badge-muted text-xs">ViewWidget</span>
        </div>
        @include('panel::partials.widget-card', ['widget' => $widget])
    </div>
</x-panel::playground-zona>
