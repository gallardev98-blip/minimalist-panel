@php
    use MyLaravelTools\Panel\Support\PanelLayout;
    use MyLaravelTools\Panel\Support\ThemeResolver;

    $variablesClaras = ThemeResolver::lightVariables();
    $variablesOscuras = ThemeResolver::darkVariables();
    $variablesLayout = PanelLayout::variablesCss();
@endphp

<style>
    .panel-playground-root {
@foreach (array_merge($variablesClaras, $variablesLayout) as $nombre => $valor)
        --{{ $nombre }}: {{ $valor }};
@endforeach
        --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06);
        --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.12);
    }

    .panel-playground-root .panel-body,
    .panel-playground-root.panel-playground-body {
        font-family: {!! ThemeResolver::fontFamily() !!};
    }

    .panel-playground-root .dark {
@foreach ($variablesOscuras as $nombre => $valor)
        --{{ $nombre }}: {{ $valor }};
@endforeach
        --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.45);
        --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.35);
    }
</style>
