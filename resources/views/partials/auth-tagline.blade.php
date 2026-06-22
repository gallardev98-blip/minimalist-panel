@php
    use MyLaravelTools\Panel\Support\PanelLayout;

    $tagline = PanelLayout::marca('tagline');
    $mostrar = (bool) PanelLayout::authUi('show_tagline', true);
@endphp

@if ($mostrar && is_string($tagline) && $tagline !== '')
    <p class="panel-auth-subtitle">{{ $tagline }}</p>
@endif
