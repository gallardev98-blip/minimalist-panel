<a
    href="{{ route('panel.dashboard') }}"
    wire:navigate
    wire:navigate.hover
    @click="sidebarOpen = false"
    class="panel-nav-group-trigger panel-nav-static-item"
>
    <span class="panel-nav-icon">
        <x-panel::icon name="layout-dashboard" class="h-4 w-4" />
    </span>
    <span class="min-w-0 flex-1 truncate">{{ __('panel::panel.breadcrumbs.dashboard') }}</span>
</a>

<div x-data="{ openGroup: @json($openGroupIndex ?? null) }">
    @foreach ($navigation as $index => $item)
        @if (($item['type'] ?? 'link') === 'group')
            <div class="panel-nav-group">
                <button
                    type="button"
                    @click="openGroup = openGroup === {{ $index }} ? null : {{ $index }}"
                    class="panel-nav-group-trigger"
                    :class="{ 'panel-nav-group-trigger-open': openGroup === {{ $index }} }"
                    :aria-expanded="openGroup === {{ $index }}"
                >
                    <span class="panel-nav-icon">
                        <x-panel::icon :name="$item['icon'] ?? 'folder'" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0 flex-1 truncate text-left">{{ $item['label'] }}</span>
                    <x-panel::icon
                        name="chevron-down"
                        class="panel-nav-group-chevron h-4 w-4 shrink-0 transition-transform"
                        x-bind:class="{ 'rotate-180': openGroup === {{ $index }} }"
                    />
                </button>

                <div x-show="openGroup === {{ $index }}" x-cloak class="panel-nav-group-children">
                    @foreach ($item['children'] ?? [] as $child)
                        @php
                            $isCurrent = MyLaravelTools\Panel\Support\NavigationBuilder::linkIsCurrent($child);
                        @endphp
                        <a
                            href="{{ $child['url'] }}"
                            wire:navigate
                            wire:navigate.hover
                            @click="openGroup = {{ $index }}; sidebarOpen = false"
                            class="panel-nav-sublink {{ $isCurrent ? 'panel-nav-sublink-active' : '' }}"
                        >
                            <span class="min-w-0 flex-1 truncate">{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @php
                $iconName = $item['icon'] ?? 'layers';
                $isCurrent = MyLaravelTools\Panel\Support\NavigationBuilder::linkIsCurrent($item);
            @endphp
            <a
                href="{{ $item['url'] }}"
                wire:navigate
                wire:navigate.hover
                @click="sidebarOpen = false"
                class="panel-nav-link {{ $isCurrent ? 'panel-nav-link-active' : '' }}"
            >
                <span class="panel-nav-icon">
                    <x-panel::icon :name="$iconName" class="h-4 w-4" />
                </span>
                <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</div>
