@php
    use MyLaravelTools\Panel\Support\NavigationBuilder;

    $openGroupIndex = null;

    foreach ($navigation as $index => $item) {
        if (($item['type'] ?? 'link') === 'group' && ($item['open'] ?? false)) {
            $openGroupIndex = $index;

            break;
        }
    }
@endphp

<div
    class="panel-topbar-nav-scroll"
    x-data="{ openGroup: @json($openGroupIndex) }"
    @click.outside="openGroup = null"
>
    <a
        href="{{ route('panel.dashboard') }}"
        wire:navigate
        wire:navigate.hover
        class="panel-topbar-trigger {{ request()->routeIs('panel.dashboard') ? 'panel-topbar-trigger-active' : '' }}"
    >
        <x-panel::icon name="layout-dashboard" class="h-4 w-4 shrink-0" />
        <span>{{ __('panel::panel.breadcrumbs.dashboard') }}</span>
    </a>

    @foreach ($navigation as $index => $item)
        @if (($item['type'] ?? 'link') === 'group')
            @php $grupoActivo = NavigationBuilder::groupHasCurrentChild($item); @endphp
            <div class="panel-topbar-dropdown">
                <button
                    type="button"
                    @click="openGroup = openGroup === {{ $index }} ? null : {{ $index }}"
                    class="panel-topbar-trigger {{ $grupoActivo ? 'panel-topbar-trigger-active' : '' }}"
                    :class="{ 'panel-topbar-trigger-open': openGroup === {{ $index }} }"
                    :aria-expanded="openGroup === {{ $index }}"
                >
                    <x-panel::icon :name="$item['icon'] ?? 'folder'" class="h-4 w-4 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                    <x-panel::icon
                        name="chevron-down"
                        class="panel-topbar-chevron h-4 w-4 shrink-0 transition-transform"
                        x-bind:class="{ 'rotate-180': openGroup === {{ $index }} }"
                    />
                </button>

                <div
                    x-show="openGroup === {{ $index }}"
                    x-cloak
                    x-transition:enter="panel-topbar-dropdown-enter"
                    x-transition:leave="panel-topbar-dropdown-leave"
                    class="panel-topbar-dropdown-panel"
                    role="menu"
                >
                    @foreach ($item['children'] ?? [] as $child)
                        @php $activo = NavigationBuilder::linkIsCurrent($child); @endphp
                        <a
                            href="{{ $child['url'] }}"
                            wire:navigate
                            wire:navigate.hover
                            @click="openGroup = null"
                            class="panel-topbar-sublink {{ $activo ? 'panel-topbar-sublink-active' : '' }}"
                            role="menuitem"
                        >
                            <x-panel::icon :name="$child['icon'] ?? 'circle'" class="h-4 w-4 shrink-0 opacity-70" />
                            <span class="min-w-0 flex-1 truncate">{{ $child['label'] }}</span>
                            @if (! empty($child['badge']))
                                <span class="panel-nav-badge">{{ $child['badge'] }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @php $activo = NavigationBuilder::linkIsCurrent($item); @endphp
            <a
                href="{{ $item['url'] }}"
                wire:navigate
                wire:navigate.hover
                class="panel-topbar-trigger {{ $activo ? 'panel-topbar-trigger-active' : '' }}"
                title="{{ $item['label'] }}"
            >
                <x-panel::icon :name="$item['icon'] ?? 'layers'" class="h-4 w-4 shrink-0" />
                <span>{{ $item['label'] }}</span>
                @if (! empty($item['badge']))
                    <span class="panel-nav-badge">{{ $item['badge'] }}</span>
                @endif
            </a>
        @endif
    @endforeach
</div>
