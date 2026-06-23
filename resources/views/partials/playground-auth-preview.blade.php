@php
    use MyLaravelTools\Panel\Support\PanelLayout;

    $esSplit = PanelLayout::authUi('layout', 'centered') === 'split';
    $imagen = PanelLayout::urlImagenAuth();
    $fondo = PanelLayout::urlFondoAuth();
    $imagenDemo = $imagen ?: 'linear-gradient(135deg, var(--panel-primary) 0%, var(--panel-accent, #6366f1) 100%)';
    $esGradiente = is_string($imagenDemo) && (str_starts_with($imagenDemo, 'linear-gradient') || str_starts_with($imagenDemo, 'radial-gradient'));
@endphp

<x-panel::playground-zona zona="auth" :$zonasModificadas :$zonaResaltada class="mb-8">
    <div class="panel-card overflow-hidden">
        <div class="panel-border flex items-center justify-between border-b px-4 py-3">
            <div>
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.auth_preview_title') }}</h2>
                <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.auth_preview_desc') }}</p>
            </div>
            <span class="panel-badge {{ $esSplit ? 'panel-badge-success' : 'panel-badge-muted' }} text-xs">
                {{ $esSplit ? __('panel::panel.documentation.auth_layout_split') : __('panel::panel.documentation.auth_layout_centered') }}
            </span>
        </div>

        <div
            class="panel-playground-auth-preview panel-playground-auth-preview--{{ $esSplit ? 'split' : 'centered' }}"
            @if ($fondo)
                style="--panel-auth-preview-bg: {{ str_starts_with((string) $fondo, 'linear-gradient') || str_starts_with((string) $fondo, 'radial-gradient') ? $fondo : "url('{$fondo}')" }};"
            @endif
        >
            <div class="panel-playground-auth-preview-shell {{ $esSplit ? 'panel-playground-auth-preview-shell--split' : '' }}">
                <div class="panel-playground-auth-preview-card">
                    <div class="panel-playground-auth-preview-brand">
                        <span class="panel-playground-auth-preview-logo"></span>
                        <span class="panel-heading text-xs font-bold">{{ $marca ?? config('panel.brand.name', 'Panel') }}</span>
                    </div>
                    <div class="panel-playground-auth-preview-fields">
                        <span class="panel-playground-auth-preview-field"></span>
                        <span class="panel-playground-auth-preview-field"></span>
                        <span class="panel-playground-auth-preview-btn"></span>
                    </div>
                </div>
                @if ($esSplit)
                    <div
                        class="panel-playground-auth-preview-image"
                        style="background: {{ $esGradiente ? $imagenDemo : "url('{$imagenDemo}') center/cover no-repeat" }};"
                    ></div>
                @endif
            </div>
        </div>
    </div>
</x-panel::playground-zona>
