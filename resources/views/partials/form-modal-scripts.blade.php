<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('panelFormularioModal', (slug, recordId, borradorActivo, focoActivo) => ({
            avisoBorrador: false,
            init() {
                this.$nextTick(() => {
                    if (focoActivo) {
                        this.enfocarPrimerCampo();
                    }
                    if (borradorActivo && ! recordId) {
                        this.restaurarBorrador();
                    }
                });

                if (borradorActivo && ! recordId) {
                    let temporizador;
                    this.$watch('$wire.form', (valor) => {
                        clearTimeout(temporizador);
                        temporizador = setTimeout(() => this.guardarBorrador(valor), 400);
                    }, { deep: true });
                }

                window.addEventListener('panel-borrador-limpiado', (evento) => {
                    if (evento.detail?.slug !== slug || ! evento.detail?.esNuevo) {
                        return;
                    }
                    localStorage.removeItem(this.clave());
                    this.avisoBorrador = false;
                });
            },
            clave() {
                return `panel-borrador-${slug}-nuevo`;
            },
            restaurarBorrador() {
                const guardado = localStorage.getItem(this.clave());
                if (! guardado) {
                    return;
                }
                try {
                    const datos = JSON.parse(guardado);
                    if (datos && typeof datos === 'object') {
                        this.$wire.set('form', { ...this.$wire.form, ...datos }, false);
                        this.avisoBorrador = true;
                    }
                } catch {
                    localStorage.removeItem(this.clave());
                }
            },
            guardarBorrador(valor) {
                const tieneDatos = Object.values(valor ?? {}).some((v) => {
                    if (Array.isArray(v)) {
                        return v.length > 0;
                    }
                    return v !== '' && v !== null && v !== false;
                });
                if (! tieneDatos) {
                    localStorage.removeItem(this.clave());
                    this.avisoBorrador = false;
                    return;
                }
                localStorage.setItem(this.clave(), JSON.stringify(valor));
                this.avisoBorrador = true;
            },
            enfocarPrimerCampo() {
                const campo = this.$root.querySelector(
                    '.panel-modal-body input:not([type=hidden]):not([type=checkbox]):not([type=radio]), .panel-modal-body textarea, .panel-modal-body select',
                );
                campo?.focus({ preventScroll: true });
            },
        }));
    });
</script>
