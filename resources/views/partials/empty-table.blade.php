@php
    $enPapelera = ($trashed ?? '') === 'only';
    $tieneFiltros = $hasActiveFilters ?? false;
@endphp

<div class="panel-empty-table">
  @if ($enPapelera)
    <x-panel::icon name="archive" class="panel-empty-table-icon" />
    <p class="panel-empty-table-title">{{ __('panel::panel.empty_trashed') }}</p>
  @elseif ($soloBusquedaActiva ?? false)
    <x-panel::icon name="search" class="panel-empty-table-icon" />
    <p class="panel-empty-table-title">{{ __('panel::panel.empty_search', ['query' => $search]) }}</p>
    <p class="panel-empty-table-hint">{{ __('panel::panel.empty_search_hint') }}</p>
    <button type="button" wire:click="$set('search', '')" class="panel-btn panel-btn-ghost panel-btn-compact mt-4">
      <x-panel::icon name="x" class="h-4 w-4" />
      {{ __('panel::panel.clear_search') }}
    </button>
  @elseif ($tieneFiltros)
    <x-panel::icon name="clipboard-list" class="panel-empty-table-icon" />
    <p class="panel-empty-table-title">{{ __('panel::panel.empty_filtered') }}</p>
    <p class="panel-empty-table-hint">{{ __('panel::panel.empty_filtered_hint') }}</p>
    <button type="button" wire:click="resetFilters" class="panel-btn panel-btn-ghost panel-btn-compact mt-4">
      <x-panel::icon name="rotate-ccw" class="h-4 w-4" />
      {{ __('panel::panel.reset_filters') }}
    </button>
  @else
    <x-panel::icon name="archive" class="panel-empty-table-icon" />
    <p class="panel-empty-table-title">{{ __('panel::panel.no_records') }}</p>
    @if (($canCreate ?? false) && ($trashed ?? '') !== 'only')
      <p class="panel-empty-table-hint">{{ __('panel::panel.empty_hint') }}</p>
      @if ($formsInModal ?? false)
        <button type="button" wire:click="openCreateFormModal" class="panel-btn panel-btn-primary panel-btn-compact mt-4">
          <x-panel::icon name="plus" class="h-4 w-4" />
          {{ __('panel::panel.create_resource', ['label' => $resourceLabel ?? '']) }}
        </button>
      @else
        <a
          href="{{ panel_route('resources.create', ['resource' => $resourceSlug ?? '']) }}"
          class="panel-btn panel-btn-primary panel-btn-compact mt-4"
          wire:navigate wire:navigate.hover
        >
          <x-panel::icon name="plus" class="h-4 w-4" />
          {{ __('panel::panel.create_resource', ['label' => $resourceLabel ?? '']) }}
        </a>
      @endif
    @endif
  @endif
</div>
