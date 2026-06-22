<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php use MyLaravelTools\Panel\Support\ThemeResolver; @endphp
    <title>{{ __('panel::panel.documentation.playground_title') }}</title>
    <link href="{{ ThemeResolver::googleFontsUrl() }}" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
    @include('panel::partials.layout-variables')
    @include('panel::partials.theme-styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="panel-playground-body antialiased">
    {{ $slot }}
    @include('panel::partials.playground-carga')
    @livewireScripts
    @stack('panel-scripts')
</body>
</html>
