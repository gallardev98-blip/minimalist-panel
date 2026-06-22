@php
    use MyLaravelTools\Panel\Support\PanelLayout;

    $enlaces = PanelLayout::enlacesFooter();
@endphp

@if ($enlaces !== [])
    <nav class="panel-sidebar-footer-links" aria-label="{{ __('panel::panel.footer_links') }}">
        @foreach ($enlaces as $enlace)
            @php
                $url = $enlace['url'] ?? (isset($enlace['route']) ? route($enlace['route']) : '#');
                $externo = (bool) ($enlace['external'] ?? false);
            @endphp
            <a
                href="{{ $url }}"
                @if ($externo) target="_blank" rel="noopener noreferrer" @endif
                class="panel-sidebar-footer-link"
            >
                {{ $enlace['label'] ?? '' }}
            </a>
        @endforeach
    </nav>
@endif
