<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('panel.brand.name', 'Panel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php use Panel\Minimalist\Support\ThemeResolver; @endphp
    <link href="{{ ThemeResolver::googleFontsUrl() }}" rel="stylesheet">
    <script>
        window.panelDefaultTheme = @json(config('panel.theme.default', 'dark'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('panel::partials.theme-styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body
    class="panel-body panel-auth-body h-full antialiased"
    x-data="panelGuestApp()"
    x-init="init()"
>
    <div class="panel-auth-bg" aria-hidden="true"></div>

    <button
        type="button"
        @click="toggleTheme()"
        class="panel-auth-theme-toggle panel-btn-icon"
        aria-label="{{ __('panel::panel.theme_toggle') }}"
    >
        <span x-show="theme === 'dark'">
            <x-panel::icon name="sun" class="h-4 w-4" />
        </span>
        <span x-show="theme === 'light'" x-cloak>
            <x-panel::icon name="moon" class="h-4 w-4" />
        </span>
    </button>

    <div class="panel-auth-shell">
        <main class="panel-auth-card">
            <header class="panel-auth-brand">
                <a
                    href="{{ route(\Panel\Minimalist\Support\PanelAuth::loginRouteName()) }}"
                    class="panel-auth-brand-link"
                    wire:navigate
                >
                    <div class="panel-auth-brand-mark">
                        @include('panel::partials.brand-mark')
                    </div>
                    <span class="panel-auth-brand-name">{{ config('panel.brand.name', 'Panel') }}</span>
                </a>
            </header>

            <div class="panel-auth-card-body">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts

    <script>
        function panelGuestApp() {
            return {
                theme: window.panelDefaultTheme || 'dark',
                init() {
                    const saved = localStorage.getItem('panel-theme');
                    if (saved === 'light' || saved === 'dark') {
                        this.theme = saved;
                    }
                    this.applyTheme();
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
</body>
</html>
