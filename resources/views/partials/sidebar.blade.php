@php
    use MyLaravelTools\Panel\Support\Package;
    use MyLaravelTools\Panel\Support\ResourceRegistry;

    $panelPath = config('panel.path', 'admin');
    $navigation = app(ResourceRegistry::class)->navigation();
    $panelVersion = config('panel.version') ?? ('v'.Package::VERSION);

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

    <div class="panel-border panel-sidebar-footer border-t p-4">
        @auth(config('panel.guard'))
            @php
                $user = auth(config('panel.guard'))->user();
                $initial = strtoupper(substr($user?->name ?? $user?->email ?? '?', 0, 1));
            @endphp

            @if (\MyLaravelTools\Panel\Support\PanelImpersonation::bannerEnabled() && \MyLaravelTools\Panel\Support\PanelImpersonation::isActive())
                @include('panel::partials.impersonation-banner')
            @endif

            @if (\MyLaravelTools\Panel\Support\PanelAuth::profileEnabled())
                <a
                    href="{{ route('panel.profile') }}"
                    wire:navigate
                    wire:navigate.hover
                    class="panel-profile-link"
                    aria-label="{{ __('panel::panel.profile.title') }}"
                >
                    <span class="panel-user-avatar">{{ $initial }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="panel-heading block truncate text-sm font-semibold">{{ $user?->name ?? $user?->email }}</span>
                        @if ($user?->name && $user?->email)
                            <span class="panel-muted block truncate text-xs">{{ $user->email }}</span>
                        @endif
                    </span>
                    <x-panel::icon name="chevron-right" class="panel-profile-link-icon h-4 w-4 shrink-0" />
                </a>
            @endif

            <div class="panel-sidebar-toolbar">
                @if (\MyLaravelTools\Panel\Support\PanelLocale::selectorEnabled())
                    <livewire:panel.locale-switcher />
                @endif

                <button
                    type="button"
                    @click="toggleTheme()"
                    class="panel-btn-icon panel-sidebar-toolbar-btn"
                    aria-label="{{ __('panel::panel.theme_toggle') }}"
                >
                    <span x-show="theme === 'dark'">
                        <x-panel::icon name="sun" class="h-4 w-4" />
                    </span>
                    <span x-show="theme === 'light'" x-cloak>
                        <x-panel::icon name="moon" class="h-4 w-4" />
                    </span>
                </button>

                <span class="panel-sidebar-version panel-muted">{{ $panelVersion }}</span>

                <form method="POST" action="{{ route(\MyLaravelTools\Panel\Support\PanelAuth::logoutRouteName()) }}" class="panel-sidebar-logout-form">
                    @csrf
                    <button
                        type="submit"
                        class="panel-btn-icon panel-sidebar-toolbar-btn panel-sidebar-logout-btn"
                        aria-label="{{ __('panel::panel.logout') }}"
                        title="{{ __('panel::panel.logout') }}"
                    >
                        <x-panel::icon name="log-out" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        @endauth
    </div>
</aside>
