@if ($showConfirm ?? false)
    <div
        class="fixed inset-0 z-[60] flex items-center justify-center px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-confirm-title"
    >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelConfirm"></div>

        <div class="panel-card relative w-full max-w-md p-6 shadow-2xl">
            <h2 id="panel-confirm-title" class="panel-heading text-lg font-semibold">
                {{ __('panel::panel.confirm_title') }}
            </h2>
            <p class="panel-muted mt-2 text-sm">{{ $confirmMessage }}</p>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" wire:click="cancelConfirm" class="panel-btn panel-btn-ghost">
                    {{ __('panel::panel.cancel') }}
                </button>
                <button type="button" wire:click="executeConfirm" class="panel-btn panel-btn-danger">
                    {{ __('panel::panel.confirm') }}
                </button>
            </div>
        </div>
    </div>
@endif
