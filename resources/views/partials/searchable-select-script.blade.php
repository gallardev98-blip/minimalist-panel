<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('panelSearchableSelect', (config) => ({
            abierto: false,
            cerrando: false,
            busqueda: '',
            valor: null,
            seleccionLocal: null,
            opciones: config.opciones ?? [],
            multiple: config.multiple ?? false,
            deshabilitado: config.deshabilitado ?? false,
            teleport: config.teleport ?? false,
            placeholder: config.placeholder ?? '',
            textoSeleccionados: config.textoSeleccionados ?? '',
            sinResultados: config.sinResultados ?? '',
            indiceFoco: -1,
            estiloDropdown: '',
            _reposicionar: null,
            _cierreTimer: null,
            duracionCierre: config.duracionCierre ?? 100,

            initSelect() {
                this.seleccionLocal = this.valor;

                this.$watch('valor', (nuevo) => {
                    if (! this.abierto && ! this.cerrando) {
                        this.seleccionLocal = nuevo;
                    }
                });

                this._reposicionar = () => {
                    if (this.abierto || this.cerrando) {
                        this.posicionarDropdown();
                    }
                };

                window.addEventListener('scroll', this._reposicionar, true);
                window.addEventListener('resize', this._reposicionar);
            },

            valorActivo() {
                if (this.abierto || this.cerrando) {
                    return this.seleccionLocal ?? this.valor;
                }

                return this.valor;
            },

            get opcionesFiltradas() {
                const termino = this.busqueda.trim().toLowerCase();

                if (! termino) {
                    return this.opciones;
                }

                return this.opciones.filter((opcion) => opcion.etiqueta.toLowerCase().includes(termino));
            },

            textoSeleccionado() {
                const referencia = this.valorActivo();

                if (this.multiple) {
                    const seleccionados = this.opciones.filter((opcion) => this.estaSeleccionado(opcion.valor, referencia));

                    if (seleccionados.length === 0) {
                        return this.placeholder;
                    }

                    if (seleccionados.length <= 2) {
                        return seleccionados.map((opcion) => opcion.etiqueta).join(', ');
                    }

                    return this.textoSeleccionados.replace(':count', String(seleccionados.length));
                }

                const actual = this.opciones.find((opcion) => String(opcion.valor) === String(referencia ?? ''));

                if (actual) {
                    return actual.etiqueta;
                }

                const vacio = this.opciones.find((opcion) => opcion.valor === '');

                if ((referencia === null || referencia === undefined || referencia === '') && vacio) {
                    return vacio.etiqueta;
                }

                return this.placeholder;
            },

            estaSeleccionado(valor, referencia = null) {
                const actual = referencia ?? this.valorActivo();

                if (this.multiple) {
                    return Array.isArray(actual) && actual.map(String).includes(String(valor));
                }

                return String(actual ?? '') === String(valor);
            },

            seleccionar(valor) {
                if (this.deshabilitado) {
                    return;
                }

                if (this.multiple) {
                    const actual = Array.isArray(this.valor) ? [...this.valor] : [];
                    const clave = String(valor);
                    const indice = actual.map(String).indexOf(clave);

                    if (indice >= 0) {
                        actual.splice(indice, 1);
                    } else {
                        actual.push(valor);
                    }

                    this.valor = actual;
                    this.seleccionLocal = actual;

                    return;
                }

                this.seleccionLocal = valor;
                this.valor = valor;
                this.cerrarAnimado();
            },

            alternar() {
                if (this.deshabilitado) {
                    return;
                }

                this.abierto ? this.cerrarAnimado() : this.abrir();
            },

            abrir() {
                clearTimeout(this._cierreTimer);
                this.cerrando = false;
                this.seleccionLocal = this.valor;
                this.busqueda = '';
                this.abierto = true;
                this.indiceFoco = -1;
                this.$nextTick(() => {
                    this.posicionarDropdown();
                    this.$refs.busqueda?.focus();
                });
            },

            cerrarAnimado(alTerminar = null) {
                if (! this.abierto && ! this.cerrando) {
                    alTerminar?.();

                    return;
                }

                this.cerrando = true;
                this.abierto = false;
                this.indiceFoco = -1;
                clearTimeout(this._cierreTimer);
                this._cierreTimer = setTimeout(() => {
                    this.cerrando = false;
                    this.busqueda = '';
                    this.estiloDropdown = '';
                    alTerminar?.();
                }, this.duracionCierre);
                if (this.teleport) {
                    this.posicionarDropdown();
                }
            },

            cerrar() {
                this.cerrarAnimado();
            },

            cerrarSiFuera(evento) {
                if (this.$refs.trigger?.contains(evento.target)) {
                    return;
                }

                this.cerrarAnimado();
            },

            posicionarDropdown() {
                const trigger = this.$refs.trigger?.getBoundingClientRect();

                if (! trigger) {
                    return;
                }

                if (this.teleport) {
                    const ancho = Math.max(trigger.width, 220);
                    const margen = 6;
                    const alturaMax = 260;
                    const espacioAbajo = window.innerHeight - trigger.bottom - margen;
                    const espacioArriba = trigger.top - margen;
                    const abrirArriba = espacioAbajo < 180 && espacioArriba > espacioAbajo;
                    let top = abrirArriba ? trigger.top - alturaMax - margen : trigger.bottom + margen;
                    let left = trigger.left;

                    top = Math.max(margen, Math.min(top, window.innerHeight - alturaMax - margen));
                    left = Math.max(margen, Math.min(left, window.innerWidth - ancho - margen));

                    this.estiloDropdown = `top:${top}px;left:${left}px;width:${ancho}px;max-height:${alturaMax}px;`;

                    return;
                }

                const margen = 8;
                const espacioAbajo = window.innerHeight - trigger.bottom - margen;
                const alturaMax = Math.min(220, Math.max(120, espacioAbajo));

                this.estiloDropdown = `max-height:${alturaMax}px;`;
            },

            moverFoco(delta) {
                const total = this.opcionesFiltradas.length;

                if (total === 0) {
                    return;
                }

                if (this.indiceFoco < 0 && delta > 0) {
                    this.indiceFoco = 0;

                    return;
                }

                this.indiceFoco = (this.indiceFoco + delta + total) % total;
            },

            confirmarFoco() {
                const opcion = this.opcionesFiltradas[this.indiceFoco];

                if (opcion) {
                    this.seleccionar(opcion.valor);
                }
            },

            manejarTecla(evento) {
                if (! this.abierto) {
                    if (evento.key === 'ArrowDown' || evento.key === 'Enter' || evento.key === ' ') {
                        evento.preventDefault();
                        this.abrir();
                    }

                    return;
                }

                if (evento.key === 'ArrowDown') {
                    evento.preventDefault();
                    this.moverFoco(1);
                } else if (evento.key === 'ArrowUp') {
                    evento.preventDefault();
                    this.moverFoco(-1);
                } else if (evento.key === 'Enter') {
                    evento.preventDefault();
                    this.confirmarFoco();
                } else if (evento.key === 'Escape') {
                    evento.preventDefault();
                    this.cerrarAnimado();
                }
            },
        }));
    });
</script>
