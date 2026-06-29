@if ($presetsFiltros ?? false)
    <div
        class="panel-filter-presets"
        x-data="panelPresetsFiltros(@js($resourceSlug ?? 'index'))"
        wire:ignore
    >
        <div class="panel-filter-presets__row">
            <template x-if="presets.length > 0">
                <div class="panel-filter-presets__lista">
                    <span class="panel-filter-presets__label">{{ __('panel::panel.filter_presets') }}</span>
                    <template x-for="preset in presets" :key="preset.id">
                        <span class="panel-filter-presets__chip">
                            <button type="button" class="panel-filter-presets__aplicar" @click="aplicar(preset)" x-text="preset.nombre"></button>
                            <button type="button" class="panel-filter-presets__quitar" @click="eliminar(preset.id)" :aria-label="@js(__('panel::panel.remove_preset'))">
                                <x-panel::icon name="x" class="h-3 w-3" />
                            </button>
                        </span>
                    </template>
                </div>
            </template>
            <button type="button" class="panel-btn panel-btn-ghost panel-btn-compact" @click="guardarActual()">
                <x-panel::icon name="bookmark" class="h-4 w-4 shrink-0" />
                <span>{{ __('panel::panel.save_preset') }}</span>
            </button>
        </div>
    </div>
@endif
