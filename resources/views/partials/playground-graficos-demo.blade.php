@php
    use MyLaravelTools\Panel\Support\PanelPlaygroundGraficos;

    $tipoActivo = (string) ($graficos['tipo_activo'] ?? 'bar');
@endphp

<x-panel::playground-zona zona="graficos" :$zonasModificadas :$zonaResaltada class="panel-playground-zona--graficos">
    <section class="panel-playground-charts mb-8" wire:key="graficos-{{ $revisionGraficos }}">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.charts_preview') }}</h2>
            <span class="panel-muted text-xs">{{ __('panel::panel.documentation.charts_preview_hint') }}</span>
        </div>

        <div class="panel-playground-charts-grid">
            @foreach ($widgetsGraficos as $widget)
                @php $tipo = $widget->getChartType(); @endphp
                <div @class([
                    'panel-widget-card panel-widget-card--chart panel-playground-chart-card',
                    'panel-playground-chart-card--active' => $tipo === $tipoActivo,
                ])>
                    <div class="mb-2 flex items-center justify-between">
                        <span class="panel-muted text-xs font-semibold uppercase tracking-wide">
                            {{ PanelPlaygroundGraficos::etiquetaTipo($tipo) }}
                        </span>
                        @if ($tipo === $tipoActivo)
                            <span class="panel-playground-chart-activo">{{ __('panel::panel.documentation.chart_editing') }}</span>
                        @endif
                    </div>
                    @include('panel::partials.playground-chart', ['widget' => $widget])
                </div>
            @endforeach
        </div>
    </section>
</x-panel::playground-zona>
