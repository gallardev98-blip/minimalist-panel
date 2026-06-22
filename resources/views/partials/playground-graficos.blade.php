@php
    use MyLaravelTools\Panel\Support\PanelPlaygroundGraficos;
@endphp

<div class="panel-playground-graficos-panel">
    <header class="panel-playground-section-intro">
        <h2 class="panel-heading text-sm font-bold">{{ __('panel::panel.documentation.charts_title') }}</h2>
        <p class="panel-muted mt-1 text-xs leading-relaxed">{{ __('panel::panel.documentation.charts_desc') }}</p>
    </header>

    <p class="panel-playground-group-label">{{ __('panel::panel.documentation.chart_type_pick') }}</p>
    <div class="panel-playground-chart-tipos mb-4">
        @foreach (PanelPlaygroundGraficos::TIPOS as $tipo)
            <button
                type="button"
                wire:click="seleccionarTipoGrafico('{{ $tipo }}')"
                @class([
                    'panel-playground-chart-tipo',
                    'panel-playground-chart-tipo--active' => ($graficos['tipo_activo'] ?? 'bar') === $tipo,
                ])
            >
                {{ PanelPlaygroundGraficos::etiquetaTipo($tipo) }}
            </button>
        @endforeach
    </div>

    <div class="panel-playground-option-list">
        <div class="panel-playground-fila">
            <label class="panel-playground-fila-label" for="grafico-estilo">{{ __('panel::panel.documentation.chart_style') }}</label>
            <select id="grafico-estilo" wire:model.live="graficos.estilo" class="panel-input w-full text-sm">
                @foreach (PanelPlaygroundGraficos::ESTILOS as $estilo)
                    <option value="{{ $estilo }}">{{ PanelPlaygroundGraficos::etiquetaEstilo($estilo) }}</option>
                @endforeach
            </select>
        </div>

        <div class="panel-playground-fila">
            <label class="panel-playground-fila-label" for="grafico-altura">{{ __('panel::panel.documentation.chart_height') }}</label>
            <p class="panel-playground-fila-ayuda">{{ __('panel::panel.documentation.chart_height_hint') }}</p>
            <input
                id="grafico-altura"
                type="range"
                min="120"
                max="400"
                step="10"
                wire:model.live="graficos.altura"
                class="panel-playground-range w-full"
            >
            <p class="panel-muted mt-1 text-xs">{{ $graficos['altura'] ?? 200 }} px</p>
        </div>

        <div class="panel-playground-fila">
            <label class="panel-playground-fila-label" for="grafico-radio">{{ __('panel::panel.documentation.chart_radius') }}</label>
            <input
                id="grafico-radio"
                type="range"
                min="0"
                max="20"
                step="2"
                wire:model.live="graficos.borde_radio"
                class="panel-playground-range w-full"
            >
            <p class="panel-muted mt-1 text-xs">{{ $graficos['borde_radio'] ?? 12 }} px</p>
        </div>

        @if (($graficos['tipo_activo'] ?? 'bar') === 'doughnut')
            <div class="panel-playground-fila">
                <label class="panel-playground-fila-label" for="grafico-cutout">{{ __('panel::panel.documentation.chart_cutout') }}</label>
                <input
                    id="grafico-cutout"
                    type="range"
                    min="40"
                    max="85"
                    step="1"
                    wire:model.live="graficos.cutout"
                    class="panel-playground-range w-full"
                >
                <p class="panel-muted mt-1 text-xs">{{ $graficos['cutout'] ?? 72 }}%</p>
            </div>
        @endif

        <div class="panel-playground-fila panel-playground-fila--toggles">
            <label class="panel-playground-switch-row">
                <span class="panel-playground-fila-label">{{ __('panel::panel.documentation.chart_gradient') }}</span>
                <span class="panel-playground-switch">
                    <input type="checkbox" wire:model.live="graficos.degradado" class="panel-playground-switch-input">
                    <span class="panel-playground-switch-track" aria-hidden="true"></span>
                </span>
            </label>
            <label class="panel-playground-switch-row">
                <span class="panel-playground-fila-label">{{ __('panel::panel.documentation.chart_animation') }}</span>
                <span class="panel-playground-switch">
                    <input type="checkbox" wire:model.live="graficos.animacion" class="panel-playground-switch-input">
                    <span class="panel-playground-switch-track" aria-hidden="true"></span>
                </span>
            </label>
            <label class="panel-playground-switch-row">
                <span class="panel-playground-fila-label">{{ __('panel::panel.documentation.chart_legend') }}</span>
                <span class="panel-playground-switch">
                    <input type="checkbox" wire:model.live="graficos.leyenda" class="panel-playground-switch-input">
                    <span class="panel-playground-switch-track" aria-hidden="true"></span>
                </span>
            </label>
            <label class="panel-playground-switch-row">
                <span class="panel-playground-fila-label">{{ __('panel::panel.documentation.chart_theme_colors') }}</span>
                <span class="panel-playground-switch">
                    <input type="checkbox" wire:model.live="graficos.colores_tema" class="panel-playground-switch-input">
                    <span class="panel-playground-switch-track" aria-hidden="true"></span>
                </span>
            </label>
        </div>

        @if ($graficos['colores_tema'] ?? true)
            <div class="panel-playground-fila">
                <label class="panel-playground-fila-label" for="grafico-claves">{{ __('panel::panel.documentation.chart_color_keys') }}</label>
                <p class="panel-playground-fila-ayuda">{{ __('panel::panel.documentation.chart_color_keys_hint') }}</p>
                <input
                    id="grafico-claves"
                    type="text"
                    wire:model.live.blur="graficos.claves_color"
                    placeholder="primary, accent, success"
                    class="panel-input w-full text-sm"
                >
            </div>
        @endif
    </div>

    <div class="panel-playground-exportar-bloque mt-4">
        <div class="panel-playground-exportar-cabecera">
            <span class="panel-heading text-xs font-semibold">{{ __('panel::panel.documentation.chart_code') }}</span>
            <button
                type="button"
                class="panel-copiar-btn panel-copiar-btn--sm"
                onclick="panelCopiarTexto(@js($codigoGrafico), this)"
            >
                <x-panel::icon name="copy" class="h-3 w-3 panel-copiar-icon" />
                <x-panel::icon name="check" class="h-3 w-3 panel-copiar-icon-ok" />
                {{ __('panel::panel.documentation.copy') }}
            </button>
        </div>
        <pre class="panel-playground-codigo"><code>{{ $codigoGrafico }}</code></pre>
    </div>
</div>
