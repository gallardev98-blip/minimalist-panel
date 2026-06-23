@php
    $items = $items ?? [];
@endphp

<ul class="panel-playground-timeline space-y-2">
    @foreach ($items as $item)
        <li class="panel-playground-timeline-item flex items-center gap-3 text-sm">
            <span class="panel-playground-timeline-dot h-2 w-2 shrink-0 rounded-full bg-[var(--panel-primary)]"></span>
            <span class="panel-heading font-medium">{{ $item['label'] ?? '' }}</span>
            <span class="panel-muted ml-auto text-xs">{{ $item['value'] ?? '' }}</span>
        </li>
    @endforeach
</ul>
