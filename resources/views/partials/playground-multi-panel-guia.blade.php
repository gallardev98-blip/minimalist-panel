@php
    use MyLaravelTools\Panel\Support\PanelMultiPanelGuia;

    $paneles = PanelMultiPanelGuia::panelesDemo();
    $esMulti = is_array(config('panel.panels')) && config('panel.panels') !== [];
@endphp

<x-panel::playground-zona zona="multipanel" :$zonasModificadas :$zonaResaltada>
    <div class="panel-card overflow-hidden">
        <div class="panel-border flex flex-wrap items-center justify-between gap-2 border-b px-4 py-3">
            <div>
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.multi_guide_title') }}</h2>
                <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.multi_guide_desc') }}</p>
            </div>
            <span @class(['panel-badge text-xs', $esMulti ? 'panel-badge-success' : 'panel-badge-muted'])>
                {{ $esMulti ? __('panel::panel.documentation.multi_guide_active') : __('panel::panel.documentation.multi_guide_single') }}
            </span>
        </div>

        <div class="grid gap-3 p-4 sm:grid-cols-2">
            @foreach ($paneles as $panel)
                <div class="panel-playground-multi-card rounded-lg border border-[color:var(--panel-border)] p-3">
                    <p class="panel-heading text-sm font-semibold">{{ $panel['id'] }}</p>
                    <p class="panel-muted mt-1 font-mono text-xs">{{ $panel['path'] }}</p>
                    <code class="panel-playground-perm-code mt-2 block text-xs">{{ $panel['rutas'] }}</code>
                </div>
            @endforeach
        </div>

        <div class="panel-border border-t p-4">
            <div class="panel-playground-exportar-cabecera mb-2">
                <p class="panel-playground-group-label">config/panel.php</p>
                <button
                    type="button"
                    class="panel-copiar-btn"
                    onclick="panelCopiarTexto(@js(PanelMultiPanelGuia::codigo()), this)"
                    title="{{ __('panel::panel.documentation.copy') }}"
                >
                    <x-panel::icon name="copy" class="h-3.5 w-3.5 panel-copiar-icon" />
                    <x-panel::icon name="check" class="h-3.5 w-3.5 panel-copiar-icon-ok" />
                    <span class="panel-copiar-text">{{ __('panel::panel.documentation.copy') }}</span>
                </button>
            </div>
            <pre class="panel-playground-codigo"><code>{{ PanelMultiPanelGuia::codigo() }}</code></pre>
        </div>
    </div>
</x-panel::playground-zona>
