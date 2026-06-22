@php
    use MyLaravelTools\Panel\Support\PanelLayout;
@endphp

@include('panel::partials.theme-variables')

<style>
    :root {
@foreach (PanelLayout::variablesCss() as $nombre => $valor)
        --{{ $nombre }}: {{ $valor }};
@endforeach
    }
</style>
