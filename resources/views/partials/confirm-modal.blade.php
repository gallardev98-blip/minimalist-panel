@if ($showConfirm ?? false)
    <div
        class="panel-modal-root"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-confirm-title"
        x-data
        @keydown.escape.window="$wire.cancelConfirm()"
    >
        <div class="panel-modal-backdrop" wire:click="cancelConfirm"></div>

        <div class="panel-card panel-modal-dialog panel-modal-dialog--compact">
            <h2 id="panel-confirm-title" class="panel-heading text-lg font-semibold">
                {{ __('panel::panel.confirm_title') }}
            </h2>
            <p class="panel-muted mt-2 text-sm">{{ $confirmMessage }}</p>

            <div class="panel-modal-actions">
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
