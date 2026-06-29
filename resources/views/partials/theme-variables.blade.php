@php
    use MyLaravelTools\Panel\Support\ThemeResolver;

    $light = ThemeResolver::lightVariables();
    $dark = ThemeResolver::darkVariables();
@endphp
<style>
    :root {
@foreach ($light as $name => $value)
        --{{ $name }}: {{ $value }};
@endforeach
        --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06);
        --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.12);
        --panel-header-height: 4rem;
        --panel-input-radius: var(--panel-radius);
        --panel-form-radius: calc(var(--panel-radius) + 0.25rem);
    }

    .dark {
@foreach ($dark as $name => $value)
        --{{ $name }}: {{ $value }};
@endforeach
        --panel-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.45);
        --panel-shadow-lg: 0 10px 40px -10px rgb(0 0 0 / 0.35);
    }
</style>
