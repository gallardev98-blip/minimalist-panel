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

            function panelAuthResolveTheme() {
                const saved = localStorage.getItem(storageKey);

                return saved === 'light' || saved === 'dark'
                    ? saved
                    : (window.panelDefaultTheme || 'dark');
            }

            function panelAuthSyncThemeIcons(currentTheme) {
                const darkIcon = document.getElementById('panel-auth-theme-icon-dark');
                const lightIcon = document.getElementById('panel-auth-theme-icon-light');

                if (! darkIcon || ! lightIcon) {
                    return;
                }

                const isDark = currentTheme === 'dark';
                darkIcon.hidden = ! isDark;
                lightIcon.hidden = isDark;
            }

            window.panelAuthApplyTheme = function panelAuthApplyTheme() {
                const theme = panelAuthResolveTheme();
                document.documentElement.classList.toggle('dark', theme === 'dark');
                panelAuthSyncThemeIcons(theme);
            };

            window.panelAuthToggleTheme = function panelAuthToggleTheme() {
                const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                localStorage.setItem(storageKey, nextTheme);
                window.panelAuthApplyTheme();
            };

            window.panelAuthApplyTheme();

            document.addEventListener('livewire:init', function () {
                Livewire.hook('morph.updated', function () {
                    window.panelAuthApplyTheme();
                });
            });
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @include('panel::partials.layout-variables')
    @include('panel::partials.theme-styles')
    @include('panel::partials.custom-head')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="panel-body panel-auth-body h-full antialiased {{ \MyLaravelTools\Panel\Support\PanelLayout::layoutAuthSplit() ? 'panel-auth-body--split' : '' }}">
    @php
        $fondoAuth = \MyLaravelTools\Panel\Support\PanelLayout::urlFondoAuth();
        $esGradiente = is_string($fondoAuth) && (str_starts_with($fondoAuth, 'linear-gradient') || str_starts_with($fondoAuth, 'radial-gradient'));
    @endphp
    <div
        class="panel-auth-bg"
        aria-hidden="true"
        @if ($fondoAuth)
            style="background: {{ $esGradiente ? $fondoAuth : "url('{$fondoAuth}') center/cover no-repeat" }}"
        @endif
    ></div>

    @persist('panel-auth-theme-toggle')
        <button
            type="button"
            id="panel-auth-theme-toggle"
            class="panel-auth-theme-toggle panel-btn-icon"
            aria-label="{{ __('panel::panel.theme_toggle') }}"
            onclick="window.panelAuthToggleTheme()"
        >
            <span id="panel-auth-theme-icon-dark">
                <x-panel::icon name="sun" class="h-4 w-4" />
            </span>
            <span id="panel-auth-theme-icon-light" hidden>
                <x-panel::icon name="moon" class="h-4 w-4" />
            </span>
        </button>
    @endpersist

    @if (\MyLaravelTools\Panel\Support\PanelLocale::selectorEnabled())
        <div class="panel-auth-locale">
            <livewire:panel.locale-switcher menu-placement="down" />
        </div>
    @endif

    <div class="panel-auth-shell {{ \MyLaravelTools\Panel\Support\PanelLayout::layoutAuthSplit() ? 'panel-auth-shell--split' : '' }}">
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
                @include('panel::partials.auth-tagline')
            </header>

            <div class="panel-auth-card-body">
                {{ $slot }}
            </div>
        </main>

        @if (\MyLaravelTools\Panel\Support\PanelLayout::layoutAuthSplit())
            <aside
                class="panel-auth-split-image"
                style="background-image: url('{{ \MyLaravelTools\Panel\Support\PanelLayout::urlImagenAuth() }}')"
                aria-hidden="true"
            ></aside>
        @endif
    </div>

    @persist('panel-spa-loader')
        @include('panel::partials.spa-loader')
    @endpersist

    @include('panel::partials.integrations.alertas')

    @livewireScripts

    @include('panel::partials.spa-navigation')
</body>
</html>
