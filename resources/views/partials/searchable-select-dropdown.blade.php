<div
    x-show="abierto || cerrando"
    x-ref="dropdown"
    x-bind:style="estiloDropdown"
    x-bind:class="teleport ? 'panel-searchable-select__dropdown panel-searchable-select__dropdown--teleport' : 'panel-searchable-select__dropdown panel-searchable-select__dropdown--inline'"
    x-transition:enter="panel-select-enter"
    x-transition:enter-start="panel-select-enter-start"
    x-transition:enter-end="panel-select-enter-end"
    x-transition:leave="panel-select-leave"
    x-transition:leave-start="panel-select-leave-start"
    x-transition:leave-end="panel-select-leave-end"
    @click.outside="cerrarSiFuera($event)"
    role="listbox"
    x-bind:aria-multiselectable="multiple ? 'true' : 'false'"
>
    <div class="panel-searchable-select__search">
        <x-panel::icon name="search" class="panel-searchable-select__search-icon h-4 w-4 shrink-0" />
        <input
            type="search"
            x-ref="busqueda"
            x-model="busqueda"
            class="panel-searchable-select__search-input"
            placeholder="{{ __('panel::panel.search_options') }}"
            autocomplete="off"
            @keydown.stop
        >
    </div>

    <ul class="panel-searchable-select__list">
        <template x-for="(opcion, indice) in opcionesFiltradas" :key="opcion.valor">
            <li>
                <button
                    type="button"
                    class="panel-searchable-select__option"
                    x-bind:class="{
                        'panel-searchable-select__option--selected': estaSeleccionado(opcion.valor),
                        'panel-searchable-select__option--focused': indiceFoco === indice,
                    }"
                    x-bind:aria-selected="estaSeleccionado(opcion.valor)"
                    role="option"
                    @click="seleccionar(opcion.valor)"
                    @mouseenter="indiceFoco = indice"
                >
                    <span class="panel-searchable-select__option-label" x-text="opcion.etiqueta"></span>
                    <x-panel::icon
                        name="check"
                        class="panel-searchable-select__option-check h-4 w-4 shrink-0"
                        x-show="estaSeleccionado(opcion.valor)"
                    />
                </button>
            </li>
        </template>
        <li
            x-show="opcionesFiltradas.length === 0"
            class="panel-searchable-select__empty"
            x-text="sinResultados"
        ></li>
    </ul>
</div>
