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

        (function () {
            const storageKey = 'panel-theme';
            const saved = localStorage.getItem(storageKey);
            const theme = saved === 'light' || saved === 'dark'
                ? saved
                : (window.panelDefaultTheme || 'dark');

            document.documentElement.classList.toggle('dark', theme === 'dark');

            document.addEventListener('DOMContentLoaded', function () {
                const toggle = document.getElementById('panel-auth-theme-toggle');
                const darkIcon = document.getElementById('panel-auth-theme-icon-dark');
                const lightIcon = document.getElementById('panel-auth-theme-icon-light');

                if (! toggle || ! darkIcon || ! lightIcon) {
                    return;
                }

                const syncIcons = (currentTheme) => {
                    const isDark = currentTheme === 'dark';
                    darkIcon.hidden = ! isDark;
                    lightIcon.hidden = isDark;
                };

                syncIcons(theme);

                toggle.addEventListener('click', function () {
                    const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                    localStorage.setItem(storageKey, nextTheme);
                    document.documentElement.classList.toggle('dark', nextTheme === 'dark');
                    syncIcons(nextTheme);
                });
            });
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('panel::partials.theme-styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="panel-body panel-auth-body h-full antialiased">
    <div class="panel-auth-bg" aria-hidden="true"></div>

    <button
        type="button"
        id="panel-auth-theme-toggle"
        class="panel-auth-theme-toggle panel-btn-icon"
        aria-label="{{ __('panel::panel.theme_toggle') }}"
    >
        <span id="panel-auth-theme-icon-dark">
            <x-panel::icon name="sun" class="h-4 w-4" />
        </span>
        <span id="panel-auth-theme-icon-light" hidden>
            <x-panel::icon name="moon" class="h-4 w-4" />
        </span>
    </button>

    <div class="panel-auth-shell">
        <main class="panel-auth-card">
            <header class="panel-auth-brand">
                <a
                    href="{{ route(\MyLaravelTools\Panel\Support\PanelAuth::loginRouteName()) }}"
                    class="panel-auth-brand-link"
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

    @include('panel::partials.spa-loader')

    @livewireScripts

    @include('panel::partials.spa-navigation')
</body>
</html>
