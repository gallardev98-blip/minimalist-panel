@if ($filters !== [])
    <div class="flex flex-wrap gap-4">
        @foreach ($filters as $filter)
            @php
                $name = $filter->getName();
                $meta = $filter->meta();
                $type = $filter->getType();
            @endphp

            <div class="panel-filter-field min-w-[10rem]">
                <label for="panel-filter-{{ $name }}" class="panel-filter-label">
                    {{ $filter->getLabel() }}
                </label>

                @if ($type === 'boolean' || $type === 'select')
                    <select
                        id="panel-filter-{{ $name }}"
                        wire:model.live="filterValues.{{ $name }}"
                        class="panel-input text-sm"
                    >
                        @foreach (($meta['options'] ?? []) as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif ($type === 'date-range')
                    <div class="flex gap-2">
                        <input
                            type="date"
                            wire:model.live="filterValues.{{ $name }}.from"
                            placeholder="{{ $meta['fromLabel'] ?? '' }}"
                            class="panel-input text-sm"
                            aria-label="{{ $meta['fromLabel'] ?? '' }}"
                        >
                        <input
                            type="date"
                            wire:model.live="filterValues.{{ $name }}.to"
                            placeholder="{{ $meta['toLabel'] ?? '' }}"
                            class="panel-input text-sm"
                            aria-label="{{ $meta['toLabel'] ?? '' }}"
                        >
                    </div>
                @elseif ($type === 'multi-select')
                    <select wire:model.live="filterValues.{{ $name }}" multiple class="panel-input min-h-[5rem] text-sm">
                        @foreach (($meta['options'] ?? []) as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
    </div>
@endif
