<button
    type="button"
    class="panel-btn panel-btn-ghost panel-btn-compact panel-copy-link"
    x-data="{ copiado: false }"
    @click="
        navigator.clipboard?.writeText(window.location.href).then(() => {
            copiado = true;
            window.dispatchEvent(new CustomEvent('panel-toast', {
                detail: { type: 'success', message: @js(__('panel::panel.link_copied')) }
            }));
            setTimeout(() => copiado = false, 2000);
        });
    "
    :title="@js(__('panel::panel.copy_link'))"
    aria-label="{{ __('panel::panel.copy_link') }}"
>
    <x-panel::icon name="copy" class="h-4 w-4 shrink-0" />
    <span class="hidden sm:inline" x-text="copiado ? @js(__('panel::panel.copied')) : @js(__('panel::panel.copy_link'))"></span>
</button>
