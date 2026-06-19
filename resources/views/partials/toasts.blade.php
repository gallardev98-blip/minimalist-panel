<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now();
            this.toasts.push({ id, type: detail.type ?? 'success', message: detail.message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @panel-toast.window="add($event.detail)"
    class="pointer-events-none fixed bottom-4 right-4 z-50 flex w-full max-w-sm flex-col gap-2"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition
            class="panel-toast"
            :class="{
                'panel-toast-success': toast.type === 'success',
                'panel-toast-error': toast.type === 'error',
                'panel-toast-info': toast.type !== 'success' && toast.type !== 'error',
            }"
            x-text="toast.message"
        ></div>
    </template>
</div>
