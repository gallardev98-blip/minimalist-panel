@php
    $tipo = $widget->getChartType();
    $chartId = 'panel-playground-chart-'.$tipo;
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

<div class="panel-chart-wrap panel-chart-wrap--playground {{ $widget->isProgression() ? 'panel-chart-wrap--progression' : '' }}" style="height: {{ $widget->getHeight() }}px">
    <canvas
        id="{{ $chartId }}"
        data-panel-chart
        data-panel-chart-config='@json($chartConfig)'
        aria-label="{{ $widget->getLabel() }}"
    ></canvas>
</div>
