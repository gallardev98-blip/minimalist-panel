@php
    use MyLaravelTools\Panel\Support\PanelDocumentacion;

    $grupoActual = collect($grupos)->firstWhere('id', $seccionActiva);
    $tipoGrupo = $grupoActual['tipo'] ?? 'opciones';
    $opcionesSimples = in_array($tipoGrupo, ['opciones'], true)
        ? PanelDocumentacion::opcionesGrupo($seccionActiva)
        : [];
    $seccionTecnicaActual = collect($secciones)->firstWhere('id', $seccionTecnica);
    $opcionesVivasTecnicas = $seccionTecnicaActual
        ? array_values(array_filter($seccionTecnicaActual['opciones'], fn (array $o): bool => ($o['vista_previa'] ?? false) === true))
        : [];
    $opcionesRefTecnicas = $seccionTecnicaActual
        ? array_values(array_filter($seccionTecnicaActual['opciones'], fn (array $o): bool => ($o['vista_previa'] ?? false) !== true))
        : [];
@endphp

<div @class(['panel-playground-root', 'panel-playground-root--controls-open' => $mostrarControles])>
    @include('panel::partials.playground-chart-runtime')
    <div wire:key="playground-tema-{{ $revisionTema }}">
        @include('panel::partials.playground-theme-estilos')
    </div>

    @if ($zonasModificadas !== [])
        <div class="panel-playground-zonas-leyenda" aria-live="polite">
            <span class="panel-playground-zonas-leyenda-titulo">{{ __('panel::panel.documentation.zones_changed') }}</span>
            @foreach ($zonasModificadas as $zona)
                <button
                    type="button"
                    class="panel-playground-zonas-chip"
                    wire:mouseenter="enfocarZona('{{ $zona }}')"
                    wire:mouseleave="limpiarZonaResaltada"
                >
                    {{ \MyLaravelTools\Panel\Support\PanelPlaygroundVista::etiquetaZona($zona) }}
                </button>
            @endforeach
        </div>
    @endif

    <section class="panel-playground-stage" wire:key="escenario-{{ $revisionEscenario }}">
        @include('panel::partials.playground-escenario', [
            'marca' => $marca,
            'modo' => $modo,
            'clasesTabla' => $clasesTabla,
            'zonasModificadas' => $zonasModificadas,
            'zonaResaltada' => $zonaResaltada,
            'widgetsGraficos' => $widgetsGraficos,
            'graficos' => $graficos,
            'revisionGraficos' => $revisionGraficos,
        ])
    </section>

    @unless ($mostrarControles)
        <button type="button" class="panel-playground-fab" wire:click="alternarControles" aria-label="{{ __('panel::panel.documentation.show_controls') }}">
            <x-panel::icon name="settings" class="h-4 w-4 shrink-0" />
            <span class="panel-playground-fab-text">{{ __('panel::panel.documentation.show_controls') }}</span>
        </button>
    @endunless

    @if ($mostrarControles)
        <button type="button" class="panel-playground-backdrop" wire:click="alternarControles" aria-label="{{ __('panel::panel.documentation.hide_controls') }}"></button>

        <aside @class([
            'panel-playground-drawer',
            'panel-playground-drawer--avanzado' => $tipoGrupo === 'avanzado',
        ]) role="dialog" aria-label="{{ __('panel::panel.documentation.playground_title') }}">
            <header class="panel-playground-drawer-header">
                <div class="panel-playground-drawer-brand">
                    <span class="panel-playground-drawer-icon" aria-hidden="true">
                        <x-panel::icon name="sparkles" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <p class="panel-heading truncate text-sm font-bold">{{ __('panel::panel.documentation.playground_title') }}</p>
                        <p class="panel-muted truncate text-xs">{{ __('panel::panel.documentation.fake_notice') }}</p>
                    </div>
                </div>
                <div class="panel-playground-drawer-actions">
                    <button type="button" wire:click="reiniciar" class="panel-playground-icon-btn" title="{{ __('panel::panel.documentation.reset') }}">
                        <x-panel::icon name="rotate-ccw" class="h-4 w-4" />
                        <span class="sr-only">{{ __('panel::panel.documentation.reset') }}</span>
                    </button>
                    <button type="button" wire:click="alternarControles" class="panel-playground-icon-btn" title="{{ __('panel::panel.documentation.close') }}">
                        <x-panel::icon name="x" class="h-4 w-4" />
                        <span class="sr-only">{{ __('panel::panel.documentation.close') }}</span>
                    </button>
                </div>
            </header>

            <nav class="panel-playground-grupos" aria-label="{{ __('panel::panel.documentation.sections') }}">
                @foreach ($grupos as $grupo)
                    <button
                        type="button"
                        wire:click="seleccionarSeccion('{{ $grupo['id'] }}')"
                        @class([
                            'panel-playground-grupo-btn',
                            'panel-playground-grupo-btn--active' => $seccionActiva === $grupo['id'],
                        ])
                    >
                        <x-panel::icon :name="$grupo['icono']" class="h-3.5 w-3.5 shrink-0" />
                        <span>{{ $grupo['titulo'] }}</span>
                    </button>
                @endforeach
            </nav>

            <div @class([
                'panel-playground-contenido',
                'panel-playground-contenido--avanzado' => $tipoGrupo === 'avanzado',
            ]) wire:scroll wire:key="grupo-{{ $seccionActiva }}-{{ $seccionTecnica }}">
                @if ($tipoGrupo === 'inicio')
                    @include('panel::partials.playground-inicio')
                @elseif ($tipoGrupo === 'exportar')
                    @include('panel::partials.playground-exportar', [
                        'fragmentoConfig' => $fragmentoConfig,
                        'archivoConfig' => $archivoConfig,
                        'cambios' => $cambios,
                        'tieneCambios' => $tieneCambios,
                    ])
                @elseif ($tipoGrupo === 'graficos')
                    @include('panel::partials.playground-graficos', [
                        'graficos' => $graficos,
                        'codigoGrafico' => $codigoGrafico,
                    ])
                @elseif ($tipoGrupo === 'avanzado')
                    @include('panel::partials.playground-avanzado', [
                        'secciones' => $secciones,
                        'seccionTecnica' => $seccionTecnica,
                        'seccionTecnicaActual' => $seccionTecnicaActual,
                        'opcionesVivasTecnicas' => $opcionesVivasTecnicas,
                        'opcionesRefTecnicas' => $opcionesRefTecnicas,
                        'valores' => $valores,
                        'zonasModificadas' => $zonasModificadas,
                    ])
                @else
                    <div class="panel-playground-section-intro">
                        <h2 class="panel-heading text-sm font-bold">{{ $grupoActual['titulo'] ?? '' }}</h2>
                        <p class="panel-muted mt-1 text-xs leading-relaxed">{{ $grupoActual['descripcion'] ?? '' }}</p>
                    </div>
                    <div class="panel-playground-option-list">
                        @foreach ($opcionesSimples as $opcion)
                            <div wire:key="opcion-{{ $opcion['clave'] }}">
                                @include('panel::partials.playground-opcion', [
                                    'opcion' => $opcion,
                                    'valores' => $valores,
                                    'zonasModificadas' => $zonasModificadas,
                                ])
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>
    @endif
</div>

<script>
    if (typeof panelCopiarTexto !== 'function') {
        function panelCopiarTexto(texto, boton) {
            navigator.clipboard.writeText(texto).then(() => {
                boton.classList.add('panel-copiar-btn--ok');
                setTimeout(() => boton.classList.remove('panel-copiar-btn--ok'), 1800);
            });
        }
    }

    if (!window.__panelPlaygroundScroll) {
        window.__panelPlaygroundScroll = true;

        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ component, succeed }) => {
                if (component.name !== 'panel.playground') {
                    return;
                }

                const raiz = document.querySelector('.panel-playground-root');
                if (!raiz) {
                    return;
                }

                const contenido = raiz.querySelector('.panel-playground-contenido');
                const avanzado = raiz.querySelector('.panel-playground-avanzado-panel');
                const grupos = raiz.querySelector('.panel-playground-grupos');
                const scrollTop = contenido?.scrollTop ?? 0;
                const scrollAvanzado = avanzado?.scrollTop ?? 0;
                const scrollLeft = grupos?.scrollLeft ?? 0;

                succeed(() => {
                    requestAnimationFrame(() => {
                        const nuevoContenido = raiz.querySelector('.panel-playground-contenido');
                        const nuevoAvanzado = raiz.querySelector('.panel-playground-avanzado-panel');
                        const nuevosGrupos = raiz.querySelector('.panel-playground-grupos');
                        if (nuevoContenido) {
                            nuevoContenido.scrollTop = scrollTop;
                        }
                        if (nuevoAvanzado) {
                            nuevoAvanzado.scrollTop = scrollAvanzado;
                        }
                        if (nuevosGrupos) {
                            nuevosGrupos.scrollLeft = scrollLeft;
                        }
                    });
                });
            });
        });
    }
</script>
