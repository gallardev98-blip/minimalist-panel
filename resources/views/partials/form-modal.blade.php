@if ($showFormModal ?? false)
    <div
        class="panel-modal-root"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-form-modal-title"
    >
        <div class="panel-modal-backdrop" wire:click="cancelFormModal"></div>

        <div class="panel-card panel-modal-dialog">
            <div class="panel-modal-header">
                <h2 id="panel-form-modal-title" class="panel-heading text-lg font-semibold">
                    {{ ($formRecordId ?? null) ? __('panel::panel.edit') : __('panel::panel.create') }}
                    {{ $resourceLabel }}
                </h2>
                <button
                    type="button"
                    wire:click="cancelFormModal"
                    class="panel-btn panel-btn-ghost !p-2"
                    aria-label="{{ __('panel::panel.cancel') }}"
                >
                    <x-panel::icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <form wire:submit="saveFormModal" class="panel-modal-form">
                <div class="panel-modal-body">
                    @include('panel::partials.form-schema', [
                        'formSchema' => $formSchema,
                        'hasTabs' => $hasTabs ?? false,
                    ])
                </div>

                <div class="panel-modal-footer">
                    <button type="button" wire:click="cancelFormModal" class="panel-btn panel-btn-ghost">
                        {{ __('panel::panel.cancel') }}
                    </button>
                    <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="saveFormModal">
                        <span wire:loading.remove wire:target="saveFormModal">{{ __('panel::panel.save') }}</span>
                        <span wire:loading wire:target="saveFormModal">{{ __('panel::panel.loading') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
