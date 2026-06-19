@php
    $colorClass = match ($widget->getColor()) {
        'emerald', 'green' => 'text-emerald-500',
        'rose', 'red' => 'text-rose-500',
        'amber', 'yellow' => 'text-amber-500',
        default => 'text-[rgb(var(--panel-primary))]',
    };
@endphp

@if ($widget->getUrl())
    <a href="{{ $widget->getUrl() }}" class="panel-widget-card block" wire:navigate wire:navigate.hover>
@else
    <div class="panel-widget-card">
@endif
    <div class="mb-2 flex items-center justify-between">
        <span class="panel-muted text-sm font-medium">{{ $widget->getLabel() }}</span>
        @if ($widget->getIcon())
            <span class="{{ $colorClass }}">
                <x-panel::icon :name="$widget->getIcon()" class="h-5 w-5" />
            </span>
        @endif
    </div>
    <p class="panel-heading text-3xl font-bold tracking-tight">{{ $widget->getValue() }}</p>
@if ($widget->getUrl())
    </a>
@else
    </div>
@endif
