@php
    use MyLaravelTools\Panel\Support\PanelLayout;
    use MyLaravelTools\Panel\Support\ResourceRegistry;

    $navigation = app(ResourceRegistry::class)->navigation();
    $modo = PanelLayout::modo();
    $soloTopbar = $modo === 'topbar';
    $esDual = $modo === 'dual';
@endphp

<header class="panel-topbar{{ $esDual ? ' panel-topbar--dual' : '' }}">
    <div class="panel-topbar-inner{{ $esDual ? ' panel-topbar-inner--sin-marca' : '' }}">
        @if (PanelLayout::muestraMarcaTopbar())
            <div class="panel-topbar-brand">
                @include('panel::partials.brand-mark')
                <a href="{{ panel_route('dashboard') }}" wire:navigate class="panel-heading truncate font-bold">
                    {{ config('panel.brand.name', 'Panel') }}
                </a>
            </div>
        @endif

        @if ($soloTopbar)
            <nav class="panel-topbar-nav" aria-label="{{ __('panel::panel.menu_open') }}">
                @include('panel::partials.nav-links-topbar', [
                    'navigation' => $navigation,
                ])
            </nav>
        @endif

        <div class="panel-topbar-actions">
            @include('panel::partials.render-slot', ['nombre' => 'topbar.end'])

            @if (PanelLayout::busquedaGlobal())
                <button
                    type="button"
                    class="panel-btn-icon"
                    @click="Livewire.dispatch('open-global-search')"
                    aria-label="{{ __('panel::panel.global_search.title') }}"
                >
                    <x-panel::icon name="search" class="h-4 w-4" />
                </button>
            @endif

            @auth(config('panel.guard'))
                @if ($soloTopbar)
                    @include('panel::partials.topbar-toolbar')
                @endif
            @endauth

            @if (PanelLayout::mostrarMenuMovil() && ($soloTopbar || PanelLayout::usaSidebar()))
                <button
                    type="button"
                    class="panel-btn-icon lg:hidden"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="{{ __('panel::panel.menu_open') }}"
                >
                    <x-panel::icon name="menu" class="h-5 w-5" />
                </button>
            @endif
        </div>
    </div>
</header>
