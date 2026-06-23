@php
    use MyLaravelTools\Panel\Support\PanelPlaygroundVista;
@endphp

<div class="panel-playground-avanzado-layout">
    <nav class="panel-playground-avanzado-nav" aria-label="{{ __('panel::panel.documentation.advanced_nav') }}">
        @foreach ($secciones as $seccion)
            @php $cambiosSeccion = PanelPlaygroundVista::contarCambiosSeccion($seccion['id']); @endphp
            <button
                type="button"
                wire:click="seleccionarSeccionTecnica('{{ $seccion['id'] }}')"
                @class([
                    'panel-playground-avanzado-link',
                    'panel-playground-avanzado-link--active' => $seccionTecnica === $seccion['id'],
                ])
            >
                <span class="truncate">{{ $seccion['titulo'] }}</span>
                @if ($cambiosSeccion > 0)
                    <span class="panel-playground-avanzado-badge">{{ $cambiosSeccion }}</span>
                @endif
            </button>
        @endforeach
    </nav>

    <div class="panel-playground-avanzado-panel" wire:scroll wire:key="avanzado-{{ $seccionTecnica }}">
        @if ($seccionTecnicaActual)
            <header class="panel-playground-avanzado-header">
                <h2 class="panel-heading text-sm font-bold">{{ $seccionTecnicaActual['titulo'] }}</h2>
                <p class="panel-muted mt-1 text-xs leading-relaxed">{{ $seccionTecnicaActual['descripcion'] }}</p>
            </header>

            @if ($opcionesVivasTecnicas !== [])
                <p class="panel-playground-group-label">{{ __('panel::panel.documentation.live_group') }}</p>
                <div class="panel-playground-option-list mb-4">
                    @foreach ($opcionesVivasTecnicas as $opcion)
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

            @if ($opcionesRefTecnicas !== [])
                <details class="panel-playground-ref-block" open>
                    <summary class="panel-playground-ref-summary">
                        {{ __('panel::panel.documentation.ref_group') }}
                        <span class="panel-playground-ref-count">{{ count($opcionesRefTecnicas) }}</span>
                    </summary>
                    <div class="panel-playground-option-list panel-playground-option-list--ref">
                        @foreach ($opcionesRefTecnicas as $opcion)
                            <div wire:key="opcion-ref-{{ $opcion['clave'] }}">
                                @include('panel::partials.playground-opcion', [
                                    'opcion' => $opcion,
                                    'valores' => $valores,
                                    'soloReferencia' => true,
                                    'zonasModificadas' => $zonasModificadas,
                                ])
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif

            @if ($seccionTecnica === 'import')
                @include('panel::partials.playground-import-preview', compact('zonasModificadas', 'zonaResaltada'))
            @endif

            @if ($seccionTecnica === 'permissions')
                @include('panel::partials.playground-permissions-preview', compact('zonasModificadas', 'zonaResaltada'))
            @endif

            @if (in_array($seccionTecnica, ['extensions', 'campos', 'widgets'], true))
                @include('panel::partials.playground-extensiones-guia', compact('zonasModificadas', 'zonaResaltada'))
                @include('panel::partials.playground-saas-guia', compact('zonasModificadas', 'zonaResaltada'))
            @endif

            @if ($seccionTecnica === 'resources')
                @include('panel::partials.playground-relaciones-guia', compact('zonasModificadas', 'zonaResaltada'))
            @endif

            @if ($seccionTecnica === 'multi_panel')
                @include('panel::partials.playground-multi-panel-guia', compact('zonasModificadas', 'zonaResaltada'))
            @endif
        @endif
    </div>
</div>
