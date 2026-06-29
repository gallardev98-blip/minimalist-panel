@if (($columnasOcultables ?? false) && count($columnasMeta ?? []) > 0)
    <button
        type="button"
        class="panel-btn panel-btn-ghost panel-btn-compact panel-column-toggle__btn"
        @click="abierto = ! abierto"
        x-bind:aria-expanded="abierto"
        aria-haspopup="menu"
    >
        <x-panel::icon name="sliders-horizontal" class="h-4 w-4 shrink-0" />
        <span class="hidden sm:inline">{{ __('panel::panel.columns') }}</span>
    </button>
    <div
        x-show="abierto"
        x-cloak
        @click.outside="abierto = false"
        @keydown.escape.window="abierto = false"
        class="panel-column-toggle__menu"
        role="menu"
    >
        @foreach ($columnasMeta as $columna)
            <label class="panel-column-toggle__item">
                <input
                    type="checkbox"
                    class="panel-checkbox"
                    x-bind:checked="esVisible(@js($columna['nombre']))"
                    @change="alternar(@js($columna['nombre']))"
                >
                <span>{{ $columna['etiqueta'] }}</span>
            </label>
        @endforeach
        <button type="button" class="panel-column-toggle__reset" @click="mostrarTodas()">
            {{ __('panel::panel.columns_show_all') }}
        </button>
    </div>
@endif
