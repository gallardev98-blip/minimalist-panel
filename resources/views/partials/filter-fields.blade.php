@php
    use MyLaravelTools\Panel\Support\PanelRendimiento;

    $debounceFecha = PanelRendimiento::debounceFechaMs();
    $debounceMulti = PanelRendimiento::debounceMultiSelectMs();
@endphp

@foreach ($filters as $filter)
    @php
        $name = $filter->getName();
        $meta = $filter->meta();
        $type = $filter->getType();
    @endphp

    <div @class([
        'panel-filter-field',
        'panel-filter-field--wide' => in_array($type, ['date-range', 'multi-select'], true),
    ])>
        <label for="panel-filter-{{ $name }}" class="panel-filter-label">
            {{ $filter->getLabel() }}
        </label>

        @if ($type === 'boolean' || $type === 'select')
            <x-panel::searchable-select
                :id="'panel-filter-' . $name"
                wire:model.live="filterValues.{{ $name }}"
                :options="$meta['options'] ?? []"
                :teleport="true"
                class="text-sm"
            />
        @elseif ($type === 'date-range')
            <div class="panel-filter-dates">
                <input
                    type="date"
                    @if ($debounceFecha > 0)
                        wire:model.live.debounce.{{ $debounceFecha }}ms="filterValues.{{ $name }}.from"
                    @else
                        wire:model.live="filterValues.{{ $name }}.from"
                    @endif
                    class="panel-input panel-input-date text-sm"
                    aria-label="{{ $meta['fromLabel'] ?? __('panel::panel.date_from') }}"
                >
                <span class="panel-filter-dates-sep" aria-hidden="true">—</span>
                <input
                    type="date"
                    @if ($debounceFecha > 0)
                        wire:model.live.debounce.{{ $debounceFecha }}ms="filterValues.{{ $name }}.to"
                    @else
                        wire:model.live="filterValues.{{ $name }}.to"
                    @endif
                    class="panel-input panel-input-date text-sm"
                    aria-label="{{ $meta['toLabel'] ?? __('panel::panel.date_to') }}"
                >
            </div>
        @elseif ($type === 'multi-select')
            @if ($debounceMulti > 0)
                <x-panel::searchable-select
                    :id="'panel-filter-' . $name"
                    wire:model.live.debounce.{{ $debounceMulti }}ms="filterValues.{{ $name }}"
                    :options="$meta['options'] ?? []"
                    :teleport="true"
                    multiple
                    class="text-sm"
                />
            @else
                <x-panel::searchable-select
                    :id="'panel-filter-' . $name"
                    wire:model.live="filterValues.{{ $name }}"
                    :options="$meta['options'] ?? []"
                    :teleport="true"
                    multiple
                    class="text-sm"
                />
            @endif
        @endif
    </div>
@endforeach
