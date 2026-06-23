<div>
    <x-panel::page-header class="mb-6">
        <a href="{{ panel_route('resources.index', ['resource' => $resourceSlug]) }}" class="panel-back-link" wire:navigate wire:navigate.hover>
            <x-panel::icon name="arrow-left" class="h-4 w-4" />
            {{ __('panel::panel.back_to', ['label' => $resourceLabel]) }}
        </a>
        <h1 class="mt-3">{{ $recordTitle }}</h1>
        <p class="panel-muted mt-1 text-sm">{{ $resourceLabel }} · #{{ $record->getKey() }}</p>
        @if ($isTrashed)
            <span class="panel-badge panel-badge-warning mt-2">{{ __('panel::panel.in_trash') }}</span>
        @endif
    </x-panel::page-header>

    @if ($canEdit && ! $isTrashed)
        <div class="mb-6 flex justify-end">
            <a
                href="{{ panel_route('resources.edit', ['resource' => $resourceSlug, 'record' => $record->getKey()]) }}"
                class="panel-btn panel-btn-primary panel-btn-compact shrink-0"
                wire:navigate
                wire:navigate.hover
            >
                <x-panel::icon name="pencil" class="h-4 w-4" />
                {{ __('panel::panel.edit') }}
            </a>
        </div>
    @endif

    <div class="panel-detail-card">
        <dl class="panel-detail-list">
            @foreach ($detailItems as $item)
                <div class="panel-detail-row">
                    <dt class="panel-detail-label">{{ $item->getLabel() }}</dt>
                    <dd class="panel-detail-value">
                        @include('panel::partials.column-value', ['column' => $item, 'record' => $record])
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>

    @foreach ($relations as $manager)
        <livewire:panel.relation-panel
            :parent-resource="$resourceSlug"
            :parent-record-id="$record->getKey()"
            :relation="$manager->getRelationship()"
            :key="'relation-' . $manager->getRelationship()"
        />
    @endforeach
</div>
