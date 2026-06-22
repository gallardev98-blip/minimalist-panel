@props(['class' => ''])

@php
    use MyLaravelTools\Panel\Support\PanelLayout;
@endphp

<div {{ $attributes->merge(['class' => trim('panel-page-header '.$class)]) }}>
    <div class="panel-page-header-start">
        {{ $slot }}
    </div>

    @if (PanelLayout::mostrarBreadcrumbs())
        @include('panel::partials.breadcrumbs')
    @endif
</div>
