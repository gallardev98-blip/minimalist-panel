@php
    use MyLaravelTools\Panel\Support\ThemeResolver;

    $light = ThemeResolver::lightVariables();
    $dark  = ThemeResolver::darkVariables();
    $font  = ThemeResolver::fontFamily();
    $fontsUrl = ThemeResolver::googleFontsUrl();
    $brandName = config('panel.brand.name', 'Panel');
    $panelPath = config('panel.path', 'admin');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? __('panel::panel.errors.default_title') }} — {{ $brandName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontsUrl }}" rel="stylesheet">
    <script>
        (function () {
            const t = localStorage.getItem('panel-theme') || @json(config('panel.theme.default', 'dark'));
            if (t === 'dark') { document.documentElement.classList.add('dark'); }
        })();
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            @foreach ($light as $nombre => $valor)
            --{{ $nombre }}: {{ $valor }};
            @endforeach
            --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06);
            --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.12);
            --panel-font: {{ $font }};
        }

        .dark {
            @foreach ($dark as $nombre => $valor)
            --{{ $nombre }}: {{ $valor }};
            @endforeach
            --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.45);
            --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.35);
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: var(--panel-font);
            background-color: rgb(var(--panel-bg));
            color: rgb(var(--panel-text));
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .panel-error-page {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .panel-error-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2.5rem;
            text-decoration: none;
            color: rgb(var(--panel-heading));
        }

        .panel-error-brand-mark {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: rgb(var(--panel-primary));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .panel-error-brand-mark svg {
            width: 1rem;
            height: 1rem;
            color: rgb(var(--panel-primary-fg));
        }

        .panel-error-brand-name {
            font-size: 0.9375rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: rgb(var(--panel-heading));
        }

        .panel-error-card {
            background: rgb(var(--panel-card));
            border: 1px solid rgb(var(--panel-border));
            border-radius: var(--panel-radius);
            box-shadow: var(--panel-shadow-lg);
            padding: 3rem 2.5rem;
            max-width: 30rem;
            width: 100%;
        }

        .panel-error-code {
            font-size: 5rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.04em;
            color: rgb(var(--panel-heading));
            margin-bottom: 1rem;
        }

        .panel-error-icon {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1.25rem;
            color: rgb(var(--panel-primary));
            opacity: 0.7;
        }

        .panel-error-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: rgb(var(--panel-heading));
            margin-bottom: 0.5rem;
        }

        .panel-error-desc {
            font-size: 0.875rem;
            color: rgb(var(--panel-muted));
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .panel-error-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .panel-error-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1.125rem;
            border-radius: calc(var(--panel-radius) * 0.75);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.15s, color 0.15s, border-color 0.15s;
            font-family: var(--panel-font);
        }

        .panel-error-btn-primary {
            background: rgb(var(--panel-primary));
            color: rgb(var(--panel-primary-fg));
        }

        .panel-error-btn-primary:hover {
            background: rgb(var(--panel-primary-hover));
        }

        .panel-error-btn-ghost {
            background: transparent;
            color: rgb(var(--panel-muted));
            border: 1px solid rgb(var(--panel-border));
        }

        .panel-error-btn-ghost:hover {
            background: rgb(var(--panel-elevated));
            color: rgb(var(--panel-heading));
        }

        .panel-error-footer {
            margin-top: 3rem;
            font-size: 0.75rem;
            color: rgb(var(--panel-muted));
        }

        .panel-error-divider {
            width: 2.5rem;
            height: 2px;
            background: rgb(var(--panel-primary) / 0.2);
            border-radius: 9999px;
            margin: 1.25rem auto;
        }

        .panel-error-theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            border: 1px solid rgb(var(--panel-border));
            background: rgb(var(--panel-surface));
            color: rgb(var(--panel-muted));
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .panel-error-theme-toggle:hover {
            background: rgb(var(--panel-elevated));
            color: rgb(var(--panel-heading));
        }

        .panel-error-theme-toggle svg {
            width: 1rem;
            height: 1rem;
        }

        @media (max-width: 480px) {
            .panel-error-card {
                padding: 2rem 1.5rem;
            }

            .panel-error-code {
                font-size: 3.75rem;
            }
        }
    </style>
</head>
<body>
    <button
        type="button"
        class="panel-error-theme-toggle"
        aria-label="{{ __('panel::panel.theme_toggle') }}"
        onclick="(function(){const d=document.documentElement;const t=d.classList.toggle('dark');localStorage.setItem('panel-theme',t?'dark':'light');})()"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="dark-icon" style="display:none">
            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 1.78a1 1 0 011.42 1.42l-.71.7a1 1 0 11-1.41-1.41l.7-.71zM17 10a1 1 0 100 2h1a1 1 0 100-2h-1zM4.22 15.78a1 1 0 001.42-1.42l-.71-.7a1 1 0 10-1.41 1.41l.7.71zM10 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.78-.22a1 1 0 001.42 1.42l.7-.71a1 1 0 10-1.41-1.41l-.71.7zM4 10a1 1 0 100-2H3a1 1 0 000 2h1zm12.78-4.22a1 1 0 00-1.42-1.42l-.7.71a1 1 0 101.41 1.41l.71-.7zM10 6a4 4 0 100 8 4 4 0 000-8z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="light-icon">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </button>
    <script>
        (function () {
            const d = document.documentElement;
            const btn = document.querySelector('.panel-error-theme-toggle');
            if (! btn) return;
            function sync() {
                const isDark = d.classList.contains('dark');
                btn.querySelector('.dark-icon').style.display = isDark ? '' : 'none';
                btn.querySelector('.light-icon').style.display = isDark ? 'none' : '';
            }
            sync();
            btn.addEventListener('click', sync);
        })();
    </script>

    <div class="panel-error-page">
        <a href="/{{ $panelPath }}" class="panel-error-brand">
            <div class="panel-error-brand-mark">
                @include('panel::partials.brand-mark')
            </div>
            <span class="panel-error-brand-name">{{ $brandName }}</span>
        </a>

        <div class="panel-error-card">
            @yield('contenido')
        </div>

        <p class="panel-error-footer">{{ $brandName }} &mdash; {{ config('app.name') }}</p>
    </div>
</body>
</html>
