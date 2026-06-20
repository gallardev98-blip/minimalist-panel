<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('panel.brand.name', 'Panel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php use MyLaravelTools\Panel\Support\ThemeResolver; @endphp
    <link href="{{ ThemeResolver::googleFontsUrl() }}" rel="stylesheet">
    <script>
        window.panelDefaultTheme = @json(config('panel.theme.default', 'dark'));

        function panelApp() {
            return {
                sidebarOpen: false,
                theme: window.panelDefaultTheme || 'dark',
                init() {
                    const saved = localStorage.getItem('panel-theme');
                    if (saved === 'light' || saved === 'dark') {
                        this.theme = saved;
                    }

                    this.applyTheme();

                    document.addEventListener('keydown', (event) => {
                        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                            event.preventDefault();
                            Livewire.dispatch('open-global-search');
                        }
                    });
                },
                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('panel-theme', this.theme);
                    this.applyTheme();
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.theme === 'dark');
                },
            };
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('panel::partials.theme-styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body
    class="panel-body h-full antialiased"
    x-data="panelApp()"
    x-init="init()"
>
    <div x-show="sidebarOpen" x-cloak class="panel-overlay fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="panel-shell">
        @include('panel::partials.sidebar', [
            'brandName' => $brandName ?? config('panel.brand.name'),
        ])

        <main id="panel-main" class="panel-main p-6 lg:p-8">
            <div class="panel-main-content">
                {{ $slot }}
            </div>
        </main>
    </div>

    @include('panel::partials.spa-loader')

    @persist('panel-toasts')
        @include('panel::partials.toasts')
    @endpersist

    <livewire:panel.global-search />

    @livewireScripts

    @include('panel::partials.spa-navigation')
</body>
</html>
