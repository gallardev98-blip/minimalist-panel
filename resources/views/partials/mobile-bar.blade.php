@php
    use MyLaravelTools\Panel\Support\PanelLayout;
@endphp

<header class="panel-mobile-bar">
    <button
        type="button"
        class="panel-btn-icon"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="{{ __('panel::panel.menu_open') }}"
    >
        <x-panel::icon name="menu" class="h-5 w-5" />
    </button>

    <a
        href="{{ $urlInicio ?? panel_route('dashboard') }}"
        @if (! isset($urlInicio))
            wire:navigate
        @endif
        class="panel-mobile-bar-brand panel-heading truncate text-sm font-bold"
    >
        {{ $brandName ?? config('panel.brand.name', 'Panel') }}
    </a>
</header>
