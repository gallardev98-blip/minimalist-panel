@php
    $isView = method_exists($widget, 'getView');
    $isChart = ! $isView && method_exists($widget, 'getChartType');
    $columnSpan = $isView && method_exists($widget, 'getColumnSpan') ? $widget->getColumnSpan() : 1;
    $spanClass = match ($columnSpan) {
        2 => 'sm:col-span-2',
        3 => 'sm:col-span-2 xl:col-span-3',
        4 => 'sm:col-span-2 xl:col-span-4',
        default => '',
    };
    $colorClass = match ($widget->getColor()) {
        'emerald', 'green' => 'text-emerald-500',
        'rose', 'red' => 'text-rose-500',
        'amber', 'yellow' => 'text-amber-500',
        default => 'text-[rgb(var(--panel-primary))]',
    };
@endphp

@if ($widget->getUrl() && ! $isChart && ! $isView)
    <a href="{{ $widget->getUrl() }}" class="panel-widget-card block {{ $spanClass }}" wire:navigate wire:navigate.hover>
@else
    <div class="panel-widget-card {{ $isChart ? 'panel-widget-card--chart' : '' }} {{ $isView ? 'panel-widget-card--view' : '' }} {{ $spanClass }}">
@endif

    @if (! $isView)
        <div class="mb-2 flex items-center justify-between">
            <span class="panel-muted text-sm font-medium">{{ $widget->getLabel() }}</span>
            @if ($widget->getIcon())
                <span class="{{ $colorClass }}">
                    <x-panel::icon :name="$widget->getIcon()" class="h-5 w-5" />
                </span>
            @endif
        </div>
    @endif

    @if ($isView)
        @include($widget->getView(), array_merge(['label' => $widget->getLabel()], $widget->getViewData()))
    @elseif ($isChart)
        @include('panel::partials.widget-chart', ['widget' => $widget])
    @else
        <p class="panel-heading text-3xl font-bold tracking-tight">{{ $widget->getValue() }}</p>
    @endif

@if ($widget->getUrl() && ! $isChart && ! $isView)
    </a>
@else
    </div>
@endif
