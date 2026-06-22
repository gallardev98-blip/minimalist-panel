@php
    use MyLaravelTools\Panel\Support\PanelPlayground;
    use MyLaravelTools\Panel\Support\PanelPlaygroundVista;

    $clave = $opcion['clave'];
    $tipo = $opcion['tipo'];
    $rutaModelo = 'valores.'.$clave;
    $esReferencia = ($soloReferencia ?? false) || $tipo === 'referencia' || $tipo === 'hook';
    $esTema = str_starts_with($clave, 'theme.');
    $fragmentoCopia = PanelPlayground::exportarEntrada($clave);
    $zona = PanelPlaygroundVista::zonaPorClave($clave);
    $estaModificada = PanelPlaygroundVista::claveEstaModificada($clave);
@endphp

<div
    @class(['panel-playground-fila', 'panel-playground-fila--ref' => $esReferencia, 'panel-playground-fila--modificada' => $estaModificada])
    wire:mouseenter="enfocarZona('{{ $zona }}')"
    wire:mouseleave="limpiarZonaResaltada"
>
    <div class="panel-playground-fila-cabecera">
        <div class="panel-playground-fila-titulos">
            <label class="panel-playground-fila-label" for="doc-{{ md5($clave) }}">{{ $opcion['etiqueta'] }}</label>
            @unless ($esReferencia)
                <p class="panel-playground-fila-zona">{{ PanelPlaygroundVista::pistaZona($clave) }}</p>
            @endunless
        </div>
        <div class="panel-playground-fila-acciones">
            @if ($estaModificada)
                <span class="panel-playground-fila-badge">{{ __('panel::panel.documentation.modified') }}</span>
            @endif
            @unless ($esReferencia)
                <button
                    type="button"
                    class="panel-copiar-btn panel-copiar-btn--sm"
                    onclick="panelCopiarTexto(@js($fragmentoCopia), this)"
                    title="{{ __('panel::panel.documentation.copy_line') }}"
                >
                    <x-panel::icon name="copy" class="h-3 w-3 panel-copiar-icon" />
                    <x-panel::icon name="check" class="h-3 w-3 panel-copiar-icon-ok" />
                </button>
            @endunless
        </div>
    </div>

    @if (! empty($opcion['descripcion']) && ! $esReferencia)
        <p class="panel-playground-fila-ayuda">{{ $opcion['descripcion'] }}</p>
    @endif

    <div class="panel-playground-fila-control">
        @if ($tipo === 'select')
            <select
                id="doc-{{ md5($clave) }}"
                wire:model.live="{{ $rutaModelo }}"
                class="panel-input w-full text-sm"
            >
                @foreach ($opcion['valores'] as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        @elseif ($tipo === 'boolean')
            <label class="panel-playground-switch" for="doc-{{ md5($clave) }}">
                <input
                    type="checkbox"
                    id="doc-{{ md5($clave) }}"
                    wire:model.live="{{ $rutaModelo }}"
                    class="panel-playground-switch-input"
                >
                <span class="panel-playground-switch-track" aria-hidden="true"></span>
            </label>
        @elseif ($tipo === 'color')
            <input
                type="color"
                id="doc-{{ md5($clave) }}"
                wire:model.live.debounce.350ms="{{ $rutaModelo }}"
                class="panel-playground-color-input"
            >
        @elseif ($tipo === 'textarea')
            <textarea
                id="doc-{{ md5($clave) }}"
                wire:model.live.blur="{{ $rutaModelo }}"
                rows="2"
                class="panel-input w-full text-sm"
            ></textarea>
        @elseif ($tipo === 'text')
            <input
                type="text"
                id="doc-{{ md5($clave) }}"
                wire:model.live.blur="{{ $rutaModelo }}"
                class="panel-input w-full text-sm"
            >
        @elseif ($esReferencia)
            <code class="panel-playground-code">{{ $opcion['ejemplo'] ?? $opcion['tipo_dato'] ?? $opcion['defecto'] ?? '' }}</code>
        @else
            <code class="panel-playground-code">{{ $opcion['ejemplo'] ?? $opcion['defecto'] ?? '' }}</code>
        @endif
    </div>
</div>
