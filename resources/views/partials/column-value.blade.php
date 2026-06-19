@php
    $value = $column->resolve($record);
@endphp

@switch($column->getType())
    @case('boolean')
        <span class="panel-badge {{ $value ? 'panel-badge-success' : 'panel-badge-muted' }}">
            {{ $value ? __('panel::panel.yes') : __('panel::panel.no') }}
        </span>
        @break

    @case('badge')
        @php
            $badgeColor = is_array($value) ? ($value['color'] ?? 'gray') : 'gray';
            $badgeText = is_array($value) ? ($value['value'] ?? '') : $value;
            $badgeClass = match ($badgeColor) {
                'green', 'emerald' => 'panel-badge-success',
                'red', 'rose' => 'panel-badge-danger',
                'yellow', 'amber' => 'panel-badge-warning',
                'blue', 'indigo', 'violet' => 'panel-badge-primary',
                default => 'panel-badge-muted',
            };
        @endphp
        <span class="panel-badge {{ $badgeClass }}">
            {{ $badgeText }}
        </span>
        @break

    @case('image')
        @if ($value)
            <img src="{{ $value }}" alt="" class="h-10 w-10 rounded-md object-cover ring-1 ring-[rgb(var(--panel-border))]">
        @else
            <span class="panel-muted">—</span>
        @endif
        @break

    @default
        @if (is_string($value) && str_contains($value, '<'))
            <div class="prose prose-sm max-w-none dark:prose-invert">{!! $value !!}</div>
        @else
            {{ $value ?? '—' }}
        @endif
@endswitch
