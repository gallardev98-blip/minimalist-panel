@if ($showFormModal ?? false)
    <div
        class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-form-modal-title"
    >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelFormModal"></div>

        <div class="panel-card relative flex max-h-[min(90vh,48rem)] w-full max-w-2xl flex-col shadow-2xl">
            <div class="panel-border flex shrink-0 items-center justify-between border-b px-6 py-4">
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

            <form wire:submit="saveFormModal" class="flex min-h-0 flex-1 flex-col">
                <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5">
                    @include('panel::partials.form-schema', [
                        'formSchema' => $formSchema,
                        'hasTabs' => $hasTabs ?? false,
                    ])
                </div>

                <div class="panel-border flex shrink-0 items-center justify-end gap-2 border-t px-6 py-4">
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
