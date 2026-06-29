@if (count($chipsCriterios ?? []) > 0)
    <div class="panel-filter-chips" role="list" aria-label="{{ __('panel::panel.active_criteria') }}">
        @foreach ($chipsCriterios as $chip)
            <span class="panel-filter-chip" role="listitem">
                <span class="panel-filter-chip__label">{{ $chip['etiqueta'] }}:</span>
                <span class="panel-filter-chip__value">{{ $chip['valor'] }}</span>
                <button
                    type="button"
                    wire:click="quitarCriterio('{{ $chip['nombre'] }}')"
                    class="panel-filter-chip__remove"
                    aria-label="{{ __('panel::panel.remove_criterion', ['label' => $chip['etiqueta']]) }}"
                >
                    <x-panel::icon name="x" class="h-3.5 w-3.5" />
                </button>
            </span>
        @endforeach
    </div>
@endif
