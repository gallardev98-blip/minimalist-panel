@php
    use MyLaravelTools\Panel\Support\ThemeResolver;

    $light = ThemeResolver::lightVariables();
    $dark  = ThemeResolver::darkVariables();
    $font  = ThemeResolver::fontFamily();
    $fontsUrl = ThemeResolver::googleFontsUrl();
    $brandName = config('panel.brand.name', 'Panel');
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
            --panel-font: {!! $font !!};
        }

        .dark {
            @foreach ($dark as $nombre => $valor)
            --{{ $nombre }}: {{ $valor }};
            @endforeach
        }

        html {
            height: 100%;
            font-family: var(--panel-font);
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: var(--panel-font);
            background-color: rgb(var(--panel-bg));
            color: rgb(var(--panel-text));
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
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

        .panel-error-code {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.04em;
            color: rgb(var(--panel-heading));
        }

        .panel-error-divider {
            width: 2.5rem;
            height: 2px;
            background: rgb(var(--panel-border));
            border-radius: 9999px;
            margin: 1.25rem auto;
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
            max-width: 26rem;
            margin: 0 auto 2rem;
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
            gap: 0.5rem;
            padding: 0.5625rem 1.25rem;
            border-radius: calc(var(--panel-radius) * 0.75);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.15s, color 0.15s, border-color 0.15s;
            font-family: var(--panel-font);
        }

        .panel-error-btn svg { width: 1rem; height: 1rem; }

        .panel-error-btn-primary {
            background: rgb(var(--panel-primary));
            color: rgb(var(--panel-primary-fg));
        }

        .panel-error-btn-primary:hover { background: rgb(var(--panel-primary-hover)); }

        .panel-error-btn-ghost {
            background: transparent;
            color: rgb(var(--panel-muted));
            border: 1px solid rgb(var(--panel-border));
        }

        .panel-error-btn-ghost:hover {
            color: rgb(var(--panel-heading));
            border-color: rgb(var(--panel-primary) / 0.3);
        }

        .panel-error-theme-toggle {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.5rem;
            border: 1px solid rgb(var(--panel-border));
            background: transparent;
            color: rgb(var(--panel-muted));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s;
        }

        .panel-error-theme-toggle:hover {
            color: rgb(var(--panel-heading));
            border-color: rgb(var(--panel-primary) / 0.3);
        }

        .panel-error-theme-toggle svg { width: 1rem; height: 1rem; }

        @@media (max-width: 480px) {
            .panel-error-code { font-size: 3.25rem; }
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
        <span class="dark-icon" style="display:none"><x-panel::icon name="sun" /></span>
        <span class="light-icon"><x-panel::icon name="moon" /></span>
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
        @yield('contenido')
    </div>
</body>
</html>
