@php
    use Panel\Minimalist\Support\ResourceRegistry;

    $panelPath = config('panel.path', 'admin');
    $navigation = app(ResourceRegistry::class)->navigation();

    $openGroupIndex = null;

    foreach ($navigation as $index => $item) {
        if (($item['type'] ?? 'link') === 'group' && ($item['open'] ?? false)) {
            $openGroupIndex = $index;

            break;
        }
    }
@endphp

<aside
    class="panel-sidebar fixed inset-y-0 left-0 z-50 flex -translate-x-full flex-col transition-transform duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="panel-border flex h-16 items-center gap-3 border-b px-5">
        @include('panel::partials.brand-mark')
        <a
            href="{{ route('panel.dashboard') }}"
            wire:navigate
            wire:navigate.hover
            class="panel-heading truncate text-base font-bold tracking-tight"
        >
            {{ $brandName }}
        </a>
    </div>

    <nav class="panel-nav-scroll flex-1 space-y-0.5 overflow-y-auto p-3">
        @include('panel::partials.nav-links', [
            'navigation' => $navigation,
            'panelPath' => $panelPath,
            'openGroupIndex' => $openGroupIndex,
        ])
    </nav>

    <div class="panel-border space-y-3 border-t p-4">
        @auth(config('panel.guard'))
            <form method="POST" action="{{ route(\Panel\Minimalist\Support\PanelAuth::logoutRouteName()) }}">
                @csrf
                <button type="submit" class="panel-btn panel-btn-danger w-full justify-center text-sm">
                    <x-panel::icon name="log-out" class="h-4 w-4" />
                    {{ __('panel::panel.logout') }}
                </button>
            </form>
        @endauth

        <div class="mt-4 flex items-center justify-between gap-3">
            <button
                type="button"
                @click="toggleTheme()"
                class="panel-btn-icon"
                aria-label="{{ __('panel::panel.theme_toggle') }}"
            >
                <span x-show="theme === 'dark'">
                    <x-panel::icon name="sun" class="h-4 w-4" />
                </span>
                <span x-show="theme === 'light'" x-cloak>
                    <x-panel::icon name="moon" class="h-4 w-4" />
                </span>
            </button>

            <p class="panel-muted text-xs">v1</p>
        </div>
    </div>
</aside>
