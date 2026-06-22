@php
    $value = $column->resolve($record);
    $tipo = $column->getType();
    $vistaColumna = \MyLaravelTools\Panel\Facades\PanelExtensions::vistaColumna($tipo);
@endphp

@if ($vistaColumna)
    @include($vistaColumna, ['column' => $column, 'record' => $record, 'value' => $value])
@else
@switch($tipo)
    @case('boolean')
        <span class="panel-badge {{ $value ? 'panel-badge-success' : 'panel-badge-muted' }}">
            {{ $value ? __('panel::panel.yes') : __('panel::panel.no') }}
        </span>
        @break

    @case('roles')
    @case('permissions')
        @if (is_array($value) && count($value) > 0)
            <div class="flex flex-wrap gap-1">
                @foreach ($value as $role)
                    <span class="panel-badge panel-badge-primary">{{ $role }}</span>
                @endforeach
            </div>
        @else
            <span class="panel-muted">—</span>
        @endif
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

    @case('color')
        @if ($value)
            <span class="inline-flex items-center gap-2">
                <span class="h-5 w-5 rounded border border-[rgb(var(--panel-border))]" style="background-color: {{ $value }}"></span>
                <span class="font-mono text-xs">{{ $value }}</span>
            </span>
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
@endif
