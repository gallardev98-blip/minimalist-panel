@php
    use MyLaravelTools\Panel\Support\PanelSaasGuia;

    $pasos = PanelSaasGuia::pasos();
@endphp

<x-panel::playground-zona zona="extensiones" :$zonasModificadas :$zonaResaltada class="mt-4">
    <div class="panel-card overflow-hidden">
        <div class="panel-border border-b px-4 py-3">
            <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.saas_guide_title') }}</h2>
            <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.saas_guide_desc') }}</p>
        </div>
        <div class="p-4">
            <ol class="panel-playground-saas-pasos mb-4 list-decimal space-y-1 pl-4 text-xs">
                @foreach ($pasos as $paso)
                    <li class="panel-muted">{{ $paso }}</li>
                @endforeach
            </ol>
            <div class="panel-playground-exportar-cabecera mb-2">
                <p class="panel-playground-group-label">CLI</p>
                <button
                    type="button"
                    class="panel-copiar-btn"
                    onclick="panelCopiarTexto(@js(PanelSaasGuia::codigo()), this)"
                    title="{{ __('panel::panel.documentation.copy') }}"
                >
                    <x-panel::icon name="copy" class="h-3.5 w-3.5 panel-copiar-icon" />
                    <x-panel::icon name="check" class="h-3.5 w-3.5 panel-copiar-icon-ok" />
                    <span class="panel-copiar-text">{{ __('panel::panel.documentation.copy') }}</span>
                </button>
            </div>
            <pre class="panel-playground-codigo"><code>{{ PanelSaasGuia::codigo() }}</code></pre>
        </div>
    </div>
</x-panel::playground-zona>
