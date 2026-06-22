<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        use MyLaravelTools\Panel\Support\PanelLayout;
        use MyLaravelTools\Panel\Support\ThemeResolver;
    @endphp
    <title>{{ PanelLayout::tituloPagina($title ?? null) }}</title>
    <link href="{{ ThemeResolver::googleFontsUrl() }}" rel="stylesheet">
    <script>
        window.panelDefaultTheme = @json(config('panel.theme.default', 'dark'));

        function panelApp() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                theme: window.panelDefaultTheme || 'dark',
                init() {
                    const saved = localStorage.getItem('panel-theme');
                    if (saved === 'light' || saved === 'dark') {
                        this.theme = saved;
                    }

                    const savedCollapsed = localStorage.getItem('panel-sidebar-collapsed');
                    if (savedCollapsed === '1') {
                        this.sidebarCollapsed = true;
                    }

                    this.applyTheme();
                    this.applySidebarCollapsed();

                    document.addEventListener('keydown', (event) => {
                        @if (PanelLayout::busquedaGlobal() && PanelLayout::atajoBusquedaGlobal())
                        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                            event.preventDefault();
                            Livewire.dispatch('open-global-search');
                        }
                        @endif
                    });
                },
                toggleSidebarCollapsed() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('panel-sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
                    this.applySidebarCollapsed();
                },
                applySidebarCollapsed() {
                    document.documentElement.classList.toggle('panel-sidebar-collapsed', this.sidebarCollapsed);
                },
                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('panel-theme', this.theme);
                    this.applyTheme();
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.theme === 'dark');
                    document.dispatchEvent(new CustomEvent('panel-theme-changed'));
                },
            };
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('panel::partials.layout-variables')
    @include('panel::partials.theme-styles')
    @include('panel::partials.custom-head')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body
    class="panel-body h-full antialiased {{ PanelLayout::claseBodyLayout() }} {{ PanelLayout::claseDensidad() }} {{ PanelLayout::claseAnchoContenido() }}"
    x-data="panelApp()"
    x-init="init()"
>
    <div x-show="sidebarOpen" x-cloak class="panel-overlay fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="panel-shell {{ PanelLayout::claseModo() }} {{ PanelLayout::clasePosicionSidebar() }}">
        @if (PanelLayout::usaSidebar() || PanelLayout::modo() === 'topbar')
            @include('panel::partials.sidebar', [
                'brandName' => $brandName ?? config('panel.brand.name'),
            ])
        @endif

        <div class="panel-main-column">
            @if (PanelLayout::modo() === 'sidebar' && PanelLayout::mostrarMenuMovil())
                @include('panel::partials.mobile-bar', [
                    'brandName' => $brandName ?? config('panel.brand.name'),
                ])
            @endif

            @if (PanelLayout::usaTopbar())
                @include('panel::partials.topbar')
            @endif

            <main id="panel-main" class="panel-main p-6 lg:p-8">
                @persist('panel-spa-loader')
                    @include('panel::partials.spa-loader')
                @endpersist

                @include('panel::partials.render-slot', ['nombre' => 'main.before'])
                <div class="panel-main-content">
                    {{ $slot }}
                </div>
                @include('panel::partials.render-slot', ['nombre' => 'main.after'])
            </main>
        </div>
    </div>

    @persist('panel-toasts')
        @include('panel::partials.toasts')
    @endpersist

    @include('panel::partials.integrations.alertas')

    @if (PanelLayout::busquedaGlobal())
        <livewire:panel.global-search />
    @endif

    @livewireScripts

    @stack('panel-scripts')

    @include('panel::partials.spa-navigation')
</body>
</html>
