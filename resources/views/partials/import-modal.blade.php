@if ($showImportModal ?? false)
    <div
        class="panel-modal-root"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-import-title"
    >
        <div class="panel-modal-backdrop" wire:click="closeImportModal"></div>

        <div class="panel-card panel-modal-dialog">
            <div class="panel-modal-header">
                <h2 id="panel-import-title" class="panel-heading text-lg font-semibold">
                    {{ __('panel::panel.import.title', ['label' => $resourceLabel]) }}
                </h2>
                <button
                    type="button"
                    wire:click="closeImportModal"
                    class="panel-btn panel-btn-ghost !p-2"
                    aria-label="{{ __('panel::panel.cancel') }}"
                >
                    <x-panel::icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <form wire:submit="processImport" class="panel-modal-form">
                <div class="panel-modal-body">
                    <div class="panel-import-template-actions">
                        <p class="panel-muted mb-3 text-sm">{{ __('panel::panel.import.template_hint') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                wire:click="downloadImportTemplateCsv"
                                class="panel-btn panel-btn-secondary panel-btn-compact"
                            >
                                <x-panel::icon name="download" class="h-4 w-4" />
                                {{ __('panel::panel.import.template_csv') }}
                            </button>
                            <button
                                type="button"
                                wire:click="downloadImportTemplateExcel"
                                class="panel-btn panel-btn-secondary panel-btn-compact"
                            >
                                <x-panel::icon name="file-spreadsheet" class="h-4 w-4" />
                                {{ __('panel::panel.import.template_excel') }}
                            </button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="panel-import-file" class="panel-label">{{ __('panel::panel.import.file') }}</label>
                        <input
                            id="panel-import-file"
                            type="file"
                            wire:model="importFile"
                            accept=".csv,.txt,.xlsx,.xls"
                            class="panel-input mt-1 w-full"
                        >
                        @error('importFile')
                            <p class="panel-field-error mt-1 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="panel-modal-footer">
                    <button type="button" wire:click="closeImportModal" class="panel-btn panel-btn-ghost">
                        {{ __('panel::panel.cancel') }}
                    </button>
                    <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="importFile,processImport">
                        <span wire:loading.remove wire:target="importFile,processImport">{{ __('panel::panel.import.action') }}</span>
                        <span wire:loading wire:target="importFile,processImport">{{ __('panel::panel.loading') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
