@php
    $tienePaginacion = $paginator->hasPages();
    $tienePerPage = count($perPageOptions ?? []) > 1;
    $idPerPage = $perPageId ?? 'main';
@endphp

@if ($tienePaginacion || $tienePerPage)
    <div class="panel-table-footer">
        <div class="panel-table-footer__left">
            @if ($tienePerPage)
                <label class="panel-table-footer__per-page" for="panel-per-page-{{ $idPerPage }}">
                    <span>{{ __('panel::panel.per_page') }}</span>
                    <x-panel::searchable-select
                        :id="'panel-per-page-' . $idPerPage"
                        wire:model.live="perPage"
                        :options="collect($perPageOptions)->mapWithKeys(fn ($opcion) => [(string) $opcion => (string) $opcion])->all()"
                        class="panel-per-page-select text-sm"
                        :teleport="true"
                    />
                </label>
            @endif
        </div>
        @if ($tienePaginacion)
            <div class="panel-table-footer__right">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
@endif
