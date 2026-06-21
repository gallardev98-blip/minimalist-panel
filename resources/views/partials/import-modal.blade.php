@if ($showImportModal ?? false)
    <div
        class="panel-modal-root"
        role="dialog"
        aria-modal="true"
        aria-labelledby="panel-import-title"
    >
        <div class="panel-modal-backdrop" wire:click="closeImportModal"></div>

        <div class="panel-card panel-modal-dialog panel-modal-dialog--wide">
            <div class="panel-modal-header">
                <h2 id="panel-import-title" class="panel-heading text-lg font-semibold">
                    @if (($importStep ?? 'upload') === 'preview')
                        {{ __('panel::panel.import.preview_title') }}
                    @else
                        {{ __('panel::panel.import.title', ['label' => $resourceLabel]) }}
                    @endif
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

            @if (($importStep ?? 'upload') === 'preview' && ($importPreview ?? null))
                <div class="panel-modal-body">
                    <p class="panel-muted mb-4 text-sm">
                        {{ __('panel::panel.import.preview_summary', [
                            'valid' => $importPreview['summary']['valid'] ?? 0,
                            'invalid' => $importPreview['summary']['invalid'] ?? 0,
                            'total' => $importPreview['summary']['total'] ?? 0,
                        ]) }}
                    </p>

                    <div class="panel-import-preview-table-wrap">
                        <table class="panel-table panel-import-preview-table">
                            <thead>
                                <tr>
                                    <th>{{ __('panel::panel.import.preview_row') }}</th>
                                    @foreach ($importPreview['fields'] ?? [] as $label)
                                        <th>{{ $label }}</th>
                                    @endforeach
                                    <th>{{ __('panel::panel.import.preview_status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($importPreview['rows'] ?? [] as $row)
                                    <tr class="{{ $row['valid'] ? 'panel-import-preview-row--valid' : 'panel-import-preview-row--invalid' }}">
                                        <td>{{ $row['row'] }}</td>
                                        @foreach ($row['cells'] as $cell)
                                            <td>{{ $cell !== '' ? $cell : '—' }}</td>
                                        @endforeach
                                        <td>
                                            @if ($row['valid'])
                                                <span class="panel-badge panel-badge-success">{{ __('panel::panel.import.preview_valid') }}</span>
                                            @else
                                                <span class="panel-badge panel-badge-danger" title="{{ $row['error'] }}">{{ __('panel::panel.import.preview_invalid') }}</span>
                                                @if ($row['error'])
                                                    <p class="panel-field-error mt-1 text-xs">{{ $row['error'] }}</p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel-modal-footer">
                    <button type="button" wire:click="backImportUpload" class="panel-btn panel-btn-ghost">
                        {{ __('panel::panel.import.preview_back') }}
                    </button>
                    <button
                        type="button"
                        wire:click="confirmImport"
                        class="panel-btn panel-btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="confirmImport"
                        @disabled(($importPreview['summary']['valid'] ?? 0) === 0)
                    >
                        {{ __('panel::panel.import.preview_confirm', ['count' => $importPreview['summary']['valid'] ?? 0]) }}
                    </button>
                </div>
            @else
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
                            @if ($importPreviewEnabled ?? true)
                                <p class="panel-muted mt-2 text-xs">{{ __('panel::panel.import.preview_hint') }}</p>
                            @endif
                            @error('importFile')
                                <p class="panel-field-error mt-1 text-sm">{{ $message }}</p>
                            @enderror
                            <div wire:loading wire:target="importFile" class="panel-muted mt-2 text-sm">
                                {{ __('panel::panel.loading') }}
                            </div>
                        </div>
                    </div>

                    @if (! ($importPreviewEnabled ?? true))
                        <div class="panel-modal-footer">
                            <button type="button" wire:click="closeImportModal" class="panel-btn panel-btn-ghost">
                                {{ __('panel::panel.cancel') }}
                            </button>
                            <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="importFile,processImport">
                                <span wire:loading.remove wire:target="importFile,processImport">{{ __('panel::panel.import.action') }}</span>
                                <span wire:loading wire:target="importFile,processImport">{{ __('panel::panel.loading') }}</span>
                            </button>
                        </div>
                    @else
                        <div class="panel-modal-footer">
                            <button type="button" wire:click="closeImportModal" class="panel-btn panel-btn-ghost">
                                {{ __('panel::panel.cancel') }}
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>
@endif
