<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('panelColumnas', (columnas, slug) => ({
            visible: {},
            abierto: false,
            init() {
                const clave = `panel-columnas-${slug}`;
                const guardado = localStorage.getItem(clave);
                const todas = Object.fromEntries(columnas.map((c) => [c.nombre, true]));
                this.visible = guardado ? { ...todas, ...JSON.parse(guardado) } : todas;
                this.$watch('visible', (valor) => localStorage.setItem(clave, JSON.stringify(valor)), { deep: true });
            },
            esVisible(nombre) {
                return this.visible[nombre] !== false;
            },
            alternar(nombre) {
                this.visible[nombre] = ! this.esVisible(nombre);
            },
            mostrarTodas() {
                columnas.forEach((c) => {
                    this.visible[c.nombre] = true;
                });
            },
        }));

        Alpine.data('panelPresetsFiltros', (slug) => ({
            presets: [],
            init() {
                const clave = `panel-presets-filtros-${slug}`;
                try {
                    this.presets = JSON.parse(localStorage.getItem(clave) ?? '[]');
                } catch {
                    this.presets = [];
                }
            },
            persistir() {
                localStorage.setItem(`panel-presets-filtros-${slug}`, JSON.stringify(this.presets));
            },
            guardarActual() {
                const nombre = window.prompt(@js(__('panel::panel.preset_name_prompt')));
                if (! nombre?.trim()) {
                    return;
                }
                this.presets.push({
                    id: Date.now(),
                    nombre: nombre.trim(),
                    busqueda: this.$wire.search ?? '',
                    filtros: JSON.parse(JSON.stringify(this.$wire.filterValues ?? {})),
                });
                this.persistir();
                window.dispatchEvent(new CustomEvent('panel-toast', {
                    detail: { type: 'success', message: @js(__('panel::panel.preset_saved')) },
                }));
            },
            aplicar(preset) {
                this.$wire.set('search', preset.busqueda ?? '');
                this.$wire.set('filterValues', preset.filtros ?? {});
            },
            eliminar(id) {
                this.presets = this.presets.filter((p) => p.id !== id);
                this.persistir();
            },
        }));
    });
</script>
