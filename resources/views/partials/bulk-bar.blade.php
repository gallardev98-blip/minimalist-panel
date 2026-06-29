@if (($selectedCount ?? 0) > 0 && ($hasBulkActions ?? false))
    <div
        class="panel-bulk-bar"
        role="region"
        aria-label="{{ __('panel::panel.bulk_bar_label') }}"
    >
        @if ($mostrarSeleccionarTodos ?? false)
            <div class="panel-bulk-bar__banner">
                <span>{{ __('panel::panel.selected_page', ['count' => $selectedCount]) }}</span>
                <button type="button" wire:click="seleccionarTodosLosResultados" class="panel-bulk-bar__select-all">
                    {{ __('panel::panel.select_all_matching', ['count' => $totalResultados ?? 0]) }}
                </button>
            </div>
        @elseif ($seleccionGlobal ?? false)
            <div class="panel-bulk-bar__banner panel-bulk-bar__banner--global">
                {{ __('panel::panel.selected_all_matching', ['count' => $selectedCount]) }}
            </div>
        @endif
        <div class="panel-bulk-bar__inner">
            <div class="panel-bulk-bar__info">
                <span class="panel-bulk-bar__count">{{ __('panel::panel.selected', ['count' => $selectedCount]) }}</span>
                <button
                    type="button"
                    wire:click="limpiarSeleccion"
                    class="panel-btn panel-btn-ghost panel-btn-compact panel-bulk-bar__clear"
                >
                    {{ __('panel::panel.clear_selection') }}
                </button>
            </div>
            <div class="panel-bulk-bar__actions">
                @foreach ($bulkActions as $action)
                    @php
                        $btnClass = match ($action->getColor()) {
                            'rose' => 'panel-btn-danger',
                            'emerald' => 'panel-btn-success',
                            default => 'panel-btn-secondary',
                        };
                    @endphp
                    <button
                        type="button"
                        wire:click="runBulkAction('{{ $action->getName() }}')"
                        class="panel-btn {{ $btnClass }} panel-btn-compact"
                    >
                        {{ $action->getLabel() }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endif
