@php
    use MyLaravelTools\Panel\Support\PanelAuth;
    use MyLaravelTools\Panel\Support\PanelLayout;
    use MyLaravelTools\Panel\Support\PanelLocale;
    use MyLaravelTools\Panel\Support\Package;

    $usuario = auth(config('panel.guard'))->user();
    $inicial = strtoupper(substr($usuario?->name ?? $usuario?->email ?? '?', 0, 1));
    $version = config('panel.version') ?? ('v'.Package::VERSION);
@endphp

@if (PanelLocale::selectorEnabled())
    <livewire:panel.locale-switcher menu-placement="down" />
@endif

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

@if (PanelLayout::mostrarVersion())
    <span class="panel-topbar-version panel-muted hidden xl:inline">{{ $version }}</span>
@endif

<div class="panel-topbar-dropdown panel-topbar-user" x-data="{ abierto: false }" @click.outside="abierto = false">
    <button
        type="button"
        class="panel-topbar-user-trigger"
        @click="abierto = !abierto"
        :aria-expanded="abierto"
        aria-label="{{ __('panel::panel.profile.title') }}"
    >
        <span class="panel-user-avatar">{{ $inicial }}</span>
        <span class="panel-topbar-user-name hidden md:inline">{{ $usuario?->name ?? $usuario?->email }}</span>
        <x-panel::icon name="chevron-down" class="panel-topbar-chevron h-4 w-4 shrink-0" x-bind:class="{ 'rotate-180': abierto }" />
    </button>

    <div x-show="abierto" x-cloak class="panel-topbar-dropdown-panel panel-topbar-user-panel" role="menu">
        @if (PanelAuth::profileEnabled())
            <a href="{{ panel_route('profile') }}" wire:navigate class="panel-topbar-sublink" role="menuitem" @click="abierto = false">
                <x-panel::icon name="user" class="h-4 w-4 shrink-0" />
                <span>{{ __('panel::panel.profile.title') }}</span>
            </a>
        @endif

        <form method="POST" action="{{ route(PanelAuth::logoutRouteName()) }}" role="none">
            @csrf
            <button type="submit" class="panel-topbar-sublink panel-topbar-sublink-btn w-full" role="menuitem">
                <x-panel::icon name="log-out" class="h-4 w-4 shrink-0" />
                <span>{{ __('panel::panel.logout') }}</span>
            </button>
        </form>
    </div>
</div>
