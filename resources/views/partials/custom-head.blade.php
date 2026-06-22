@php
    use MyLaravelTools\Panel\Support\PanelLayout;
@endphp

@if ($vista = PanelLayout::vistaHead())
    @include($vista)
@endif

@if ($css = PanelLayout::cssPersonalizado())
    <style>{!! $css !!}</style>
@endif

@php
    $favicon = PanelLayout::marca('favicon');
    $faviconUrl = null;

    if (is_string($favicon) && $favicon !== '') {
        $faviconUrl = str_starts_with($favicon, 'http') ? $favicon : asset($favicon);
    }
@endphp

@if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}">
@endif
