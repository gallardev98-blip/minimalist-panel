<div class="panel-playground-exportar">
    <div class="panel-playground-section-intro">
        <h2 class="panel-heading text-sm font-bold">{{ __('panel::panel.documentation.export_title') }}</h2>
        <p class="panel-muted mt-1 text-xs leading-relaxed">{{ __('panel::panel.documentation.export_desc') }}</p>
    </div>

    @if (! $tieneCambios)
        <div class="panel-playground-exportar-vacio">
            <x-panel::icon name="clipboard-list" class="panel-muted mx-auto h-8 w-8" />
            <p class="panel-heading mt-3 text-sm font-semibold">{{ __('panel::panel.documentation.export_empty_title') }}</p>
            <p class="panel-muted mt-1 text-xs">{{ __('panel::panel.documentation.export_empty_desc') }}</p>
            <button type="button" wire:click="seleccionarSeccion('apariencia')" class="panel-btn panel-btn-primary mt-4 text-sm">
                {{ __('panel::panel.documentation.start_customize') }}
            </button>
        </div>
    @else
        <div class="panel-playground-exportar-bloque">
            <div class="panel-playground-exportar-cabecera">
                <p class="panel-playground-group-label">{{ __('panel::panel.documentation.export_merge') }}</p>
                <button
                    type="button"
                    class="panel-copiar-btn"
                    onclick="panelCopiarTexto(@js($fragmentoConfig), this)"
                    title="{{ __('panel::panel.documentation.copy') }}"
                >
                    <x-panel::icon name="copy" class="h-3.5 w-3.5 panel-copiar-icon" />
                    <x-panel::icon name="check" class="h-3.5 w-3.5 panel-copiar-icon-ok" />
                    <span class="panel-copiar-text">{{ __('panel::panel.documentation.copy') }}</span>
                </button>
            </div>
            <p class="panel-muted mb-2 text-xs">{{ __('panel::panel.documentation.export_merge_hint') }}</p>
            <pre class="panel-playground-codigo"><code>return [
{{ $fragmentoConfig }}
];</code></pre>
        </div>

        <div class="panel-playground-exportar-bloque">
            <div class="panel-playground-exportar-cabecera">
                <p class="panel-playground-group-label">{{ __('panel::panel.documentation.export_file') }}</p>
                <button
                    type="button"
                    class="panel-copiar-btn"
                    onclick="panelCopiarTexto(@js($archivoConfig), this)"
                    title="{{ __('panel::panel.documentation.copy_all') }}"
                >
                    <x-panel::icon name="copy" class="h-3.5 w-3.5 panel-copiar-icon" />
                    <x-panel::icon name="check" class="h-3.5 w-3.5 panel-copiar-icon-ok" />
                    <span class="panel-copiar-text">{{ __('panel::panel.documentation.copy_all') }}</span>
                </button>
            </div>
            <p class="panel-muted mb-2 text-xs">{{ __('panel::panel.documentation.export_file_hint') }}</p>
            <pre class="panel-playground-codigo"><code>{{ $archivoConfig }}</code></pre>
        </div>

        <div class="panel-playground-exportar-bloque">
            <p class="panel-playground-group-label">{{ __('panel::panel.documentation.export_changes', ['count' => count($cambios)]) }}</p>
            <ul class="panel-playground-cambios">
                @foreach ($cambios as $cambio)
                    <li class="panel-playground-cambio">
                        <div class="panel-playground-cambio-info">
                            <span class="panel-heading text-xs font-semibold">{{ $cambio['etiqueta'] }}</span>
                            <code class="panel-playground-config-key">panel.{{ $cambio['clave'] }}</code>
                        </div>
                        <button
                            type="button"
                            class="panel-copiar-btn panel-copiar-btn--sm"
                            onclick="panelCopiarTexto(@js($cambio['fragmento']), this)"
                            title="{{ __('panel::panel.documentation.copy_line') }}"
                        >
                            <x-panel::icon name="copy" class="h-3 w-3 panel-copiar-icon" />
                            <x-panel::icon name="check" class="h-3 w-3 panel-copiar-icon-ok" />
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
