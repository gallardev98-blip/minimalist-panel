<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="panel-page-hero mb-0">
            <a href="{{ route('panel.resources.index', ['resource' => $resourceSlug]) }}" class="panel-back-link" wire:navigate wire:navigate.hover>
                <x-panel::icon name="arrow-left" class="h-4 w-4" />
                {{ __('panel::panel.back_to', ['label' => $resourceLabel]) }}
            </a>
            <h1 class="mt-3">{{ $recordTitle }}</h1>
            <p class="panel-muted mt-1 text-sm">{{ $resourceLabel }} · #{{ $record->getKey() }}</p>
            @if ($isTrashed)
                <span class="panel-badge panel-badge-warning mt-2">{{ __('panel::panel.in_trash') }}</span>
            @endif
        </div>

        @if ($canEdit && ! $isTrashed)
            <a
                href="{{ route('panel.resources.edit', ['resource' => $resourceSlug, 'record' => $record->getKey()]) }}"
                class="panel-btn panel-btn-primary panel-btn-compact shrink-0"
                wire:navigate wire:navigate.hover
            >
                <x-panel::icon name="pencil" class="h-4 w-4" />
                {{ __('panel::panel.edit') }}
            </a>
        @endif
    </div>

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
