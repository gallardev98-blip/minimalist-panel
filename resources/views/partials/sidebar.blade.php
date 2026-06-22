@php
    use MyLaravelTools\Panel\Support\Package;
    use MyLaravelTools\Panel\Support\PanelLayout;
    use MyLaravelTools\Panel\Support\ResourceRegistry;

    $panelPath = config('panel.path', 'admin');
    $navigation = app(ResourceRegistry::class)->navigation();
    $panelVersion = config('panel.version') ?? ('v'.Package::VERSION);
    $sidebarColapsable = PanelLayout::sidebarColapsable();
    $sidebarDerecha = PanelLayout::posicionSidebar() === 'right';
    $esDrawerMovil = PanelLayout::modo() === 'topbar';

    $openGroupIndex = null;

    foreach ($navigation as $index => $item) {
        if (($item['type'] ?? 'link') === 'group' && ($item['open'] ?? false)) {
            $openGroupIndex = $index;

            break;
        }
    }
@endphp

<aside
    class="panel-sidebar fixed inset-y-0 z-50 flex flex-col transition-transform duration-200 lg:translate-x-0 {{ $sidebarDerecha ? 'right-0 left-auto translate-x-full' : 'left-0 -translate-x-full' }} {{ $esDrawerMovil ? 'panel-sidebar--mobile-drawer' : '' }}"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="panel-chrome-header panel-border">
        <div class="panel-chrome-header-brand">
            @include('panel::partials.brand-mark')
            <a
                href="{{ route('panel.dashboard') }}"
                wire:navigate
                wire:navigate.hover
                class="panel-heading panel-sidebar-brand-text truncate text-base font-bold tracking-tight"
            >
                {{ $brandName }}
            </a>
        </div>
        <button
            type="button"
            class="panel-btn-icon panel-sidebar-close-btn lg:hidden"
            @click="sidebarOpen = false"
            aria-label="{{ __('panel::panel.documentation.close') }}"
        >
            <x-panel::icon name="x" class="h-5 w-5" />
        </button>
        @if ($sidebarColapsable)
            <button
                type="button"
                class="panel-btn-icon panel-sidebar-collapse-btn hidden lg:inline-flex"
                @click="toggleSidebarCollapsed()"
                :aria-label="sidebarCollapsed ? '{{ __('panel::panel.sidebar_expand') }}' : '{{ __('panel::panel.sidebar_collapse') }}'"
            >
                <x-panel::icon name="chevron-left" class="h-4 w-4 transition-transform" x-bind:class="{ 'rotate-180': sidebarCollapsed }" />
            </button>
        @endif
    </div>

    <nav class="panel-nav-scroll flex-1 space-y-0.5 overflow-y-auto p-3">
        @include('panel::partials.render-slot', ['nombre' => 'sidebar.before'])
        @include('panel::partials.nav-links', [
            'navigation' => $navigation,
            'panelPath' => $panelPath,
            'openGroupIndex' => $openGroupIndex,
        ])
        @include('panel::partials.render-slot', ['nombre' => 'sidebar.after'])
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

            @include('panel::partials.sidebar-footer-links')

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

                @if (PanelLayout::mostrarVersion())
                    <span class="panel-sidebar-version panel-muted">{{ $panelVersion }}</span>
                @endif

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
