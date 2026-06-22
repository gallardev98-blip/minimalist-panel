@php
    $chartId = 'panel-chart-' . md5($widget->getLabel() . spl_object_id($widget));
    $chartConfig = [
        'id' => $chartId,
        'label' => $widget->getLabel(),
        'type' => $widget->resolveChartType(),
        'isProgression' => $widget->isProgression(),
        'labels' => $widget->getChartData()['labels'] ?? [],
        'values' => $widget->getChartData()['values'] ?? [],
        'colors' => $widget->getColors(),
        'colorKeys' => $widget->getThemeColorKeys(),
        'options' => $widget->getChartOptions(),
    ];
@endphp

<div class="panel-chart-wrap {{ $widget->isProgression() ? 'panel-chart-wrap--progression' : '' }}" style="height: {{ $widget->getHeight() }}px">
    <canvas id="{{ $chartId }}" data-panel-chart aria-label="{{ $widget->getLabel() }}"></canvas>
</div>

@once
    @push('panel-scripts')
        @include('panel::partials.chart-mount-runtime')
    @endpush
@endonce

@push('panel-scripts')
    <script>
        window.panelChartConfigs[@json($chartId)] = @json($chartConfig);
        window.panelChartInitPending?.();
    </script>
@endpush
