@if ($showVistaRapida ?? false)
    <div
        class="panel-quick-view"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('panel::panel.quick_view') }}"
        @keydown.escape.window="$wire.cerrarVistaRapida()"
    >
        <button
            type="button"
            class="panel-quick-view__backdrop"
            wire:click="cerrarVistaRapida"
            aria-label="{{ __('panel::panel.cancel') }}"
        ></button>
        <aside class="panel-quick-view__panel">
            <header class="panel-quick-view__header">
                <h2 class="panel-quick-view__title">{{ __('panel::panel.quick_view') }}</h2>
                <button type="button" wire:click="cerrarVistaRapida" class="panel-quick-view__close" aria-label="{{ __('panel::panel.cancel') }}">
                    <x-panel::icon name="x" class="h-5 w-5" />
                </button>
            </header>
            @if ($vistaRapidaRegistro ?? null)
                <div class="panel-quick-view__body">
                    @foreach ($columns as $column)
                        <div class="panel-quick-view__field">
                            <span class="panel-quick-view__label">{{ $column->getLabel() }}</span>
                            <div class="panel-quick-view__value">
                                @include('panel::partials.column-value', ['column' => $column, 'record' => $vistaRapidaRegistro])
                            </div>
                        </div>
                    @endforeach
                </div>
                <footer class="panel-quick-view__footer">
                    @if (($formsInModal ?? false) && isset($idsRegistrosEditables[$vistaRapidaRegistro->getKey()]))
                        <button type="button" wire:click="abrirRegistro({{ $vistaRapidaRegistro->getKey() }}); cerrarVistaRapida()" class="panel-btn panel-btn-primary panel-btn-compact">
                            {{ __('panel::panel.edit') }}
                        </button>
                    @elseif (isset($idsRegistrosEditables[$vistaRapidaRegistro->getKey()]))
                        <a
                            href="{{ panel_route('resources.edit', ['resource' => $resourceSlug, 'record' => $vistaRapidaRegistro->getKey()]) }}"
                            class="panel-btn panel-btn-primary panel-btn-compact"
                            wire:navigate
                        >
                            {{ __('panel::panel.edit') }}
                        </a>
                    @endif
                    <button type="button" wire:click="cerrarVistaRapida" class="panel-btn panel-btn-ghost panel-btn-compact">
                        {{ __('panel::panel.cancel') }}
                    </button>
                </footer>
            @endif
        </aside>
    </div>
@endif
