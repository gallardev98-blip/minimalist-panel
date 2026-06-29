@php
    use MyLaravelTools\Panel\Support\PanelLayout;

    $modoFiltros = PanelLayout::modoFiltros();
    $abiertoDefecto = PanelLayout::filtrosAbiertosPorDefecto();
    $recordarEstado = PanelLayout::recordarEstadoFiltros();
    $filtrosActivos = (int) ($cantidadFiltrosActivos ?? 0);
@endphp

<div class="panel-filters-unified mb-4">
    <div class="panel-filters-unified__shell">
        <div class="panel-filters-unified__search-row">
            <div class="panel-filters-unified__search">
                @include('panel::partials.index-search')
            </div>
            @if (isset($textoRangoResultados))
                <div class="panel-index-meta-row">
                    <p
                        class="panel-index-meta"
                        wire:loading.class="panel-index-meta--loading"
                        wire:target="search,filterValues,resetFilters,quitarCriterio,trashed,sortBy,gotoPage,nextPage,previousPage,perPage"
                    >
                        {{ $textoRangoResultados }}
                    </p>
                    @include('panel::partials.copy-list-link')
                </div>
            @endif
        </div>

        @include('panel::partials.active-filter-chips', ['chipsCriterios' => $chipsCriterios ?? []])

        @include('panel::partials.filter-presets', [
            'presetsFiltros' => $presetsFiltros ?? false,
            'resourceSlug' => $resourceSlug ?? 'index',
        ])

        @if ($modoFiltros === 'inline')
            <div class="panel-filters panel-filters--inline panel-filters-unified__body">
                <div class="panel-filters__grid panel-filters__grid--inline">
                    @include('panel::partials.filter-fields', ['filters' => $filters])
                </div>
                @if ($hasActiveFilters ?? false)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="panel-btn panel-btn-ghost panel-btn-compact panel-filters__reset"
                        aria-label="{{ __('panel::panel.reset_filters') }}"
                    >
                        <x-panel::icon name="rotate-ccw" class="h-4 w-4" />
                        {{ __('panel::panel.reset_filters') }}
                    </button>
                @endif
            </div>
        @else
            <div
                wire:ignore
                class="panel-filters panel-filters--collapsible"
                x-data="{
                    abierto: @js($abiertoDefecto || $filtrosActivos > 0),
                    recordar: @js($recordarEstado),
                    filtrosActivos: @js($filtrosActivos),
                    mostrarReset: @js($hasActiveFilters ?? false),
                    init() {
                        if (this.recordar) {
                            const guardado = localStorage.getItem('panel-filtros-abiertos');
                            if (guardado !== null && this.filtrosActivos === 0) {
                                this.abierto = guardado === '1';
                            }
                        }
                    },
                    alternar() {
                        this.abierto = ! this.abierto;
                        if (this.recordar) localStorage.setItem('panel-filtros-abiertos', this.abierto ? '1' : '0');
                    },
                    actualizarEstado(evento) {
                        const antes = this.filtrosActivos;
                        this.filtrosActivos = evento.detail.filtros ?? 0;
                        this.mostrarReset = evento.detail.hasActive ?? false;
                        if (this.filtrosActivos > antes && this.filtrosActivos > 0) {
                            this.abierto = true;
                        }
                    }
                }"
                x-bind:class="abierto ? 'panel-filters--open' : ''"
                @panel-filtros-abrir.window="abierto = true"
                @panel-filtros-actualizados.window="actualizarEstado($event)"
            >
                <div class="panel-filters__bar">
                    <button
                        type="button"
                        class="panel-filters__toggle"
                        @click="alternar()"
                        x-bind:aria-expanded="abierto"
                    >
                        <x-panel::icon name="sliders-horizontal" class="h-4 w-4 shrink-0" />
                        <span>{{ __('panel::panel.filters') }}</span>
                        <span
                            x-show="filtrosActivos > 0"
                            x-cloak
                            class="panel-filters__badge"
                            x-text="filtrosActivos"
                        ></span>
                        <span class="panel-filters__chevron" x-bind:class="abierto ? 'panel-filters__chevron--open' : ''">
                            <x-panel::icon name="chevron-down" class="h-4 w-4 shrink-0" />
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="resetFilters"
                        x-show="mostrarReset"
                        x-cloak
                        class="panel-btn panel-btn-ghost panel-btn-compact panel-filters__clear"
                        aria-label="{{ __('panel::panel.reset_filters') }}"
                    >
                        <x-panel::icon name="rotate-ccw" class="h-4 w-4" />
                        <span class="hidden sm:inline">{{ __('panel::panel.reset_filters') }}</span>
                    </button>
                </div>

                <div
                    class="panel-filters__expand"
                    x-bind:class="abierto ? 'panel-filters__expand--open' : ''"
                >
                    <div class="panel-filters__expand-inner">
                        <div class="panel-filters__body">
                            <div class="panel-filters__grid">
                                @include('panel::partials.filter-fields', ['filters' => $filters])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
