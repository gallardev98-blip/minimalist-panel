<div class="panel-relation-section">
    @include('panel::partials.confirm-modal', [
        'showConfirm' => $showConfirm ?? false,
        'confirmMessage' => $confirmMessage ?? '',
    ])

    <div class="panel-relation-header">
        <h2 class="panel-relation-title">{{ $title }}</h2>
        @if ($canCreate && ! $showForm)
            <button type="button" wire:click="openCreateForm" class="panel-btn panel-btn-primary panel-btn-compact">
                <x-panel::icon name="plus" class="h-4 w-4" />
                {{ __('panel::panel.add') }}
            </button>
        @endif
    </div>

    @if ($showForm)
        <form wire:submit="save" class="panel-form-card panel-relation-form">
            <div class="panel-form-card-body">
                @include('panel::partials.form-schema', ['formSchema' => $formSchema])
            </div>
            <div class="panel-form-footer">
                <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('panel::panel.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('panel::panel.loading') }}</span>
                </button>
                <button type="button" wire:click="cancelForm" class="panel-btn panel-btn-ghost">
                    {{ __('panel::panel.cancel') }}
                </button>
            </div>
        </form>
    @endif

    <div class="panel-table-wrap">
        <div class="overflow-x-auto">
            <table class="panel-table">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column->getLabel() }}</th>
                        @endforeach
                        <th class="panel-table-actions-col">
                            <span class="sr-only">{{ __('panel::panel.actions') }}</span>
                            <x-panel::icon name="settings" class="panel-muted mx-auto h-4 w-4" aria-hidden="true" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr wire:key="rel-{{ $record->getKey() }}">
                            @foreach ($columns as $column)
                                <td>
                                    @include('panel::partials.column-value', ['column' => $column, 'record' => $record])
                                </td>
                            @endforeach
                            <td class="panel-table-actions-col">
                                @include('panel::partials.relation-row-actions', [
                                    'record' => $record,
                                    'canUpdate' => $childResourceClass::authorize('update', $record),
                                    'canDelete' => $childResourceClass::authorize('delete', $record),
                                    'isPivotRelation' => $isPivotRelation ?? false,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="py-8 text-center">
                                <p class="panel-muted text-sm">{{ __('panel::panel.related_empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="panel-border border-t px-4 py-3">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
