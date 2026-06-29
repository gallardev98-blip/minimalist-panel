@php
    use MyLaravelTools\Panel\Support\PanelLayout;
@endphp

@if ($showFormModal ?? false)
    @include('panel::partials.form-modal-scripts')

    <div
        class="panel-modal-root"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-form-modal-title"
        x-data="panelFormularioModal(
            @js($resourceSlug ?? 'form'),
            @js($formRecordId),
            @js(PanelLayout::borradorFormulario()),
            @js(PanelLayout::focoFormulario())
        )"
        @keydown.escape.window="$wire.cancelFormModal()"
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

            <p
                x-show="avisoBorrador && ! @js($formRecordId)"
                x-cloak
                class="panel-form-draft-hint"
            >
                <x-panel::icon name="bookmark" class="h-3.5 w-3.5 shrink-0" />
                <span>{{ __('panel::panel.form_draft_saved') }}</span>
                <button type="button" class="panel-form-draft-hint__discard" wire:click="descartarBorradorFormulario" @click="avisoBorrador = false">
                    {{ __('panel::panel.form_draft_discard') }}
                </button>
            </p>

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
