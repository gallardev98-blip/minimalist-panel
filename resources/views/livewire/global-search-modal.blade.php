<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="$wire.close()"
    class="fixed inset-0 z-50 flex items-start justify-center px-4 pt-[12vh]"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('panel::panel.global_search.title') }}"
>
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$wire.close()"></div>

    <div class="panel-card relative w-full max-w-xl overflow-hidden shadow-2xl">
        <div class="flex items-center gap-3 border-b border-[rgb(var(--panel-border))] px-4 py-3">
            <x-panel::icon name="search" class="panel-muted h-5 w-5 shrink-0" />
            <input
                type="search"
                wire:model.live.debounce.200ms="query"
                x-ref="searchInput"
                x-init="$watch('open', value => { if (value) $nextTick(() => $refs.searchInput?.focus()) })"
                placeholder="{{ __('panel::panel.global_search.placeholder') }}"
                class="panel-input !border-0 !bg-transparent !shadow-none !ring-0"
            >
            <kbd class="panel-muted hidden rounded border border-[rgb(var(--panel-border))] px-2 py-0.5 text-xs sm:inline">Esc</kbd>
        </div>

        <ul class="max-h-80 overflow-y-auto py-2">
            @forelse ($results as $result)
                <li>
                    <a
                        href="{{ $result['url'] }}"
                        wire:navigate
                        wire:click="close"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-[rgb(var(--panel-muted-bg))]"
                    >
                        <span class="panel-nav-icon shrink-0">
                            <x-panel::icon :name="$result['icon'] ?? 'file'" class="h-4 w-4" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="panel-text block truncate text-sm font-medium">{{ $result['label'] }}</span>
                            @if ($result['description'])
                                <span class="panel-muted block truncate text-xs">{{ $result['description'] }}</span>
                            @endif
                        </span>
                        <span class="panel-muted text-xs">{{ $result['type'] === 'navigation' ? __('panel::panel.global_search.navigation') : __('panel::panel.global_search.records') }}</span>
                    </a>
                </li>
            @empty
                <li class="panel-muted px-4 py-8 text-center text-sm">
                    {{ __('panel::panel.global_search.no_results') }}
                </li>
            @endforelse
        </ul>
    </div>
</div>
