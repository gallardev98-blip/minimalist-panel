@if (! $canUpdate && ! $canDelete)
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
            x-transition
            class="panel-row-actions-menu"
            role="menu"
        >
            @if ($canUpdate)
                <button
                    type="button"
                    wire:click="openEditForm({{ $record->getKey() }})"
                    class="panel-row-actions-item panel-row-actions-item-primary"
                    role="menuitem"
                    @click="open = false"
                >
                    <x-panel::icon name="pencil" class="h-4 w-4 shrink-0" />
                    <span>{{ __('panel::panel.edit') }}</span>
                </button>
            @endif

            @if ($canDelete)
                <button
                    type="button"
                    wire:click="requestDelete({{ $record->getKey() }})"
                    class="panel-row-actions-item panel-row-actions-item-danger"
                    role="menuitem"
                    @click="open = false"
                >
                    <x-panel::icon name="trash-2" class="h-4 w-4 shrink-0" />
                    <span>{{ ($isPivotRelation ?? false) ? __('panel::panel.detach') : __('panel::panel.delete') }}</span>
                </button>
            @endif
        </div>
    </div>
@endif
