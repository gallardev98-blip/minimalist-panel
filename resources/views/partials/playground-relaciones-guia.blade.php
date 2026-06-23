@php
    use MyLaravelTools\Panel\Support\PanelRelacionesGuia;
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $pasos = PanelRelacionesGuia::pasos();
    $pestanas = PlaygroundDemo::pestanasRelaciones();
    $pasoActivo = $pasoActivo ?? $pasos[0]['id'] ?? 'hasOne';
@endphp

<x-panel::playground-zona zona="relaciones" :$zonasModificadas :$zonaResaltada>
    <div class="panel-card overflow-hidden">
        <div class="panel-border border-b px-4 py-3">
            <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.rel_guide_title') }}</h2>
            <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.rel_guide_desc') }}</p>
        </div>

        <div class="panel-playground-ext-guia" x-data="{ paso: @js($pasoActivo) }">
            <nav class="panel-playground-ext-guia-nav" aria-label="{{ __('panel::panel.documentation.rel_guide_nav') }}">
                @foreach ($pasos as $indice => $paso)
                    <button
                        type="button"
                        class="panel-playground-ext-guia-tab"
                        :class="{ 'panel-playground-ext-guia-tab--active': paso === @js($paso['id']) }"
                        @click="paso = @js($paso['id'])"
                    >
                        <span class="panel-playground-ext-guia-step">{{ $indice + 1 }}</span>
                        {{ $paso['titulo'] }}
                    </button>
                @endforeach
            </nav>

            @foreach ($pasos as $paso)
                <div class="panel-playground-ext-guia-panel" x-show="paso === @js($paso['id'])" x-cloak>
                    <p class="panel-muted mb-3 text-xs leading-relaxed">{{ $paso['descripcion'] }}</p>
                    <div class="panel-playground-exportar-cabecera mb-2">
                        <p class="panel-playground-group-label">PHP</p>
                        <button
                            type="button"
                            class="panel-copiar-btn"
                            onclick="panelCopiarTexto(@js($paso['codigo']), this)"
                            title="{{ __('panel::panel.documentation.copy') }}"
                        >
                            <x-panel::icon name="copy" class="h-3.5 w-3.5 panel-copiar-icon" />
                            <x-panel::icon name="check" class="h-3.5 w-3.5 panel-copiar-icon-ok" />
                            <span class="panel-copiar-text">{{ __('panel::panel.documentation.copy') }}</span>
                        </button>
                    </div>
                    <pre class="panel-playground-codigo"><code>{{ $paso['codigo'] }}</code></pre>
                </div>
            @endforeach
        </div>

        <div class="panel-border border-t p-4" x-data="{ tab: @js($pestanas[0]['id'] ?? 'hasOne') }">
            <p class="panel-playground-group-label mb-2">{{ __('panel::panel.documentation.rel_preview_title') }}</p>
            <div class="panel-playground-rel-tabs mb-3 flex flex-wrap gap-1">
                @foreach ($pestanas as $pestana)
                    <button
                        type="button"
                        class="panel-badge text-xs"
                        :class="tab === @js($pestana['id']) ? 'panel-badge-primary' : 'panel-badge-muted'"
                        @click="tab = @js($pestana['id'])"
                    >
                        {{ $pestana['titulo'] }}
                        <span class="opacity-70">({{ $pestana['tipo'] }})</span>
                    </button>
                @endforeach
            </div>
            @foreach ($pestanas as $pestana)
                <div x-show="tab === @js($pestana['id'])" x-cloak>
                    <table class="panel-table w-full text-sm">
                        <tbody>
                            @foreach ($pestana['filas'] as $fila)
                                <tr>
                                    @foreach ($fila['cells'] as $celda)
                                        <td>{{ $celda }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</x-panel::playground-zona>
