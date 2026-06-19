@php
    $visibleActions = array_values(array_filter(
        $rowActions,
        fn ($action) => $action->isVisible($record, $resourceClass),
    ));
@endphp

@if ($visibleActions === [])
    <span class="panel-muted text-xs">—</span>
@else
    <div
        x-data="{ open: false }"
        class="panel-row-actions"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
    >
        <button
            type="button"
            class="panel-row-actions-trigger"
            @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="menu"
            aria-label="{{ __('panel::panel.row_actions_menu') }}"
        >
            <x-panel::icon name="settings" class="h-4 w-4" />
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="panel-row-actions-menu"
            role="menu"
        >
            @foreach ($visibleActions as $action)
                @php
                    $itemClass = match ($action->getColor()) {
                        'primary' => 'panel-row-actions-item-primary',
                        'rose' => 'panel-row-actions-item-danger',
                        'emerald' => 'panel-row-actions-item-success',
                        default => '',
                    };
                @endphp

                @if ($action->isLink() && ! (($formsInModal ?? false) && $action->getName() === 'edit'))
                    <a
                        href="{{ $action->resolveUrl($record, $resourceSlug) }}"
                        class="panel-row-actions-item {{ $itemClass }}"
                        role="menuitem"
                        wire:navigate wire:navigate.hover
                        @click="open = false"
                    >
                        @if ($action->getIcon())
                            <x-panel::icon :name="$action->getIcon()" class="h-4 w-4 shrink-0" />
                        @endif
                        <span>{{ $action->getLabel() }}</span>
                    </a>
                @elseif (($formsInModal ?? false) && $action->getName() === 'edit')
                    <button
                        type="button"
                        wire:click="openEditFormModal({{ $record->getKey() }})"
                        class="panel-row-actions-item {{ $itemClass }}"
                        role="menuitem"
                        @click="open = false"
                    >
                        @if ($action->getIcon())
                            <x-panel::icon :name="$action->getIcon()" class="h-4 w-4 shrink-0" />
                        @endif
                        <span>{{ $action->getLabel() }}</span>
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="requestRowAction('{{ $action->getName() }}', {{ $record->getKey() }})"
                        class="panel-row-actions-item {{ $itemClass }}"
                        role="menuitem"
                        @click="open = false"
                    >
                        @if ($action->getIcon())
                            <x-panel::icon :name="$action->getIcon()" class="h-4 w-4 shrink-0" />
                        @endif
                        <span>{{ $action->getLabel() }}</span>
                    </button>
                @endif
            @endforeach
        </div>
    </div>
@endif
