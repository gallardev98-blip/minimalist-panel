@php
    use MyLaravelTools\Panel\Support\Package;
    use MyLaravelTools\Panel\Support\PanelLayout;
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $navigation = PlaygroundDemo::navegacion();
    $usuario = PlaygroundDemo::usuario();
    $panelPath = config('panel.path', 'admin');
    $panelVersion = config('panel.version') ?? ('v'.Package::VERSION);
    $sidebarColapsable = PanelLayout::sidebarColapsable();
    $openGroupIndex = 0;
    $temaDefecto = (string) config('panel.theme.default', 'dark');
    $zonasModificadas = $zonasModificadas ?? [];
    $zonaResaltada = $zonaResaltada ?? null;
@endphp

<div
    class="panel-playground-escenario panel-body h-full {{ PanelLayout::claseBodyLayout() }} {{ PanelLayout::claseDensidad() }} {{ PanelLayout::claseAnchoContenido() }}"
    x-data="panelPlaygroundApp(@js($temaDefecto))"
    x-init="init()"
>
    <div x-show="sidebarOpen" x-cloak class="panel-overlay panel-playground-overlay" @click="sidebarOpen = false"></div>

    <x-panel::playground-zona zona="tema" :$zonasModificadas :$zonaResaltada class="panel-playground-zona--shell h-full">
    <div class="panel-shell panel-playground-shell {{ PanelLayout::claseModo() }} {{ PanelLayout::clasePosicionSidebar() }}">
        @if (PanelLayout::usaSidebar() || PanelLayout::modo() === 'topbar')
            <x-panel::playground-zona zona="menu" :$zonasModificadas :$zonaResaltada class="panel-playground-zona--menu">
                <aside
                    class="panel-sidebar panel-playground-sidebar flex h-full flex-col {{ PanelLayout::modo() === 'topbar' ? 'panel-sidebar--mobile-drawer' : '' }}"
                    :class="{ 'panel-playground-sidebar--open': sidebarOpen }"
                >
                    <x-panel::playground-zona zona="marca" :$zonasModificadas :$zonaResaltada class="panel-chrome-header panel-border">
                        @include('panel::partials.brand-mark')
                        <span class="panel-heading panel-sidebar-brand-text truncate text-base font-bold tracking-tight">{{ $marca }}</span>
                        @if ($sidebarColapsable)
                            <button type="button" class="panel-btn-icon panel-sidebar-collapse-btn ms-auto hidden lg:inline-flex" @click="toggleSidebarCollapsed()">
                                <x-panel::icon name="chevron-left" class="h-4 w-4 transition-transform" x-bind:class="{ 'rotate-180': sidebarCollapsed }" />
                            </button>
                        @endif
                    </x-panel::playground-zona>

                    <nav class="panel-nav-scroll flex-1 space-y-0.5 overflow-y-auto p-3">
                        @include('panel::partials.nav-links', [
                            'navigation' => $navigation,
                            'panelPath' => $panelPath,
                            'openGroupIndex' => $openGroupIndex,
                        ])
                    </nav>

                    <div class="panel-border panel-sidebar-footer border-t p-4">
                        <div class="panel-profile-link pointer-events-none opacity-90">
                            <span class="panel-user-avatar">{{ $usuario['initial'] }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="panel-heading block truncate text-sm font-semibold">{{ $usuario['name'] }}</span>
                                <span class="panel-muted block truncate text-xs">{{ $usuario['email'] }}</span>
                            </span>
                        </div>
                        <div class="panel-sidebar-toolbar">
                            <button type="button" @click="toggleTheme()" class="panel-btn-icon panel-sidebar-toolbar-btn" aria-label="{{ __('panel::panel.theme_toggle') }}">
                                <span x-show="theme === 'dark'"><x-panel::icon name="sun" class="h-4 w-4" /></span>
                                <span x-show="theme === 'light'" x-cloak><x-panel::icon name="moon" class="h-4 w-4" /></span>
                            </button>
                            @if (PanelLayout::mostrarVersion())
                                <span class="panel-sidebar-version panel-muted">{{ $panelVersion }}</span>
                            @endif
                        </div>
                    </div>
                </aside>
            </x-panel::playground-zona>
        @endif

        <div class="panel-main-column">
            @if (PanelLayout::usaTopbar())
                <x-panel::playground-zona zona="menu" :$zonasModificadas :$zonaResaltada>
                    @include('panel::partials.topbar')
                </x-panel::playground-zona>
            @endif

            <main class="panel-main panel-playground-main p-6 lg:p-8">
                <div class="panel-playground-fake-badge">{{ __('panel::panel.documentation.fake_badge') }}</div>
                @include('panel::pages.playground-vista-previa', [
                    'marca' => $marca,
                    'modo' => $modo,
                    'clasesTabla' => $clasesTabla,
                    'zonasModificadas' => $zonasModificadas,
                    'zonaResaltada' => $zonaResaltada,
                    'widgetsGraficos' => $widgetsGraficos,
                    'graficos' => $graficos,
                    'revisionGraficos' => $revisionGraficos,
                ])
            </main>
        </div>
    </div>
    </x-panel::playground-zona>
</div>

<script>
    if (typeof panelPlaygroundApp !== 'function') {
        const CLAVE_TEMA_PLAYGROUND = 'panel-playground-theme';
        const CLAVE_TEMA_MANUAL = 'panel-playground-theme-manual';
        const CLAVE_SIDEBAR_PLAYGROUND = 'panel-playground-sidebar-collapsed';

        function panelPlaygroundAplicarTema(nodo, tema) {
            if (!nodo) return;
            nodo.classList.toggle('dark', tema === 'dark');
        }

        function panelPlaygroundSincronizarTema(tema, forzar = false) {
            const escenario = document.querySelector('.panel-playground-escenario');
            if (!escenario?._x_dataStack?.[0]) return;
            const estado = escenario._x_dataStack[0];
            if (!forzar && sessionStorage.getItem(CLAVE_TEMA_MANUAL) === '1') return;
            estado.theme = tema;
            sessionStorage.setItem(CLAVE_TEMA_PLAYGROUND, tema);
            sessionStorage.removeItem(CLAVE_TEMA_MANUAL);
            panelPlaygroundAplicarTema(escenario, tema);
        }

        if (!window.__panelPlaygroundEventos) {
            window.__panelPlaygroundEventos = true;
            document.addEventListener('livewire:init', () => {
                Livewire.on('playground-tema-actualizado', ({ tema }) => panelPlaygroundSincronizarTema(tema, true));
                Livewire.on('playground-reiniciar-tema', ({ tema }) => {
                    sessionStorage.removeItem(CLAVE_TEMA_PLAYGROUND);
                    sessionStorage.removeItem(CLAVE_TEMA_MANUAL);
                    sessionStorage.removeItem(CLAVE_SIDEBAR_PLAYGROUND);
                    panelPlaygroundSincronizarTema(tema, true);
                });
                Livewire.on('playground-resaltar-zona', ({ zona }) => {
                    document.querySelectorAll('[data-playground-zona]').forEach((nodo) => {
                        nodo.classList.toggle('panel-playground-zona--flash', nodo.dataset.playgroundZona === zona);
                    });
                    setTimeout(() => {
                        document.querySelectorAll('.panel-playground-zona--flash').forEach((nodo) => {
                            nodo.classList.remove('panel-playground-zona--flash');
                        });
                    }, 2200);
                });
            });
        }

        function panelPlaygroundApp(temaDefecto) {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                theme: temaDefecto || 'dark',
                init() {
                    const temaGuardado = sessionStorage.getItem(CLAVE_TEMA_PLAYGROUND);
                    if (temaGuardado === 'light' || temaGuardado === 'dark') this.theme = temaGuardado;
                    if (sessionStorage.getItem(CLAVE_SIDEBAR_PLAYGROUND) === '1') this.sidebarCollapsed = true;
                    this.applyTheme();
                    this.applySidebarCollapsed();
                },
                toggleSidebarCollapsed() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    sessionStorage.setItem(CLAVE_SIDEBAR_PLAYGROUND, this.sidebarCollapsed ? '1' : '0');
                    this.applySidebarCollapsed();
                },
                applySidebarCollapsed() {
                    this.$root.classList.toggle('panel-sidebar-collapsed', this.sidebarCollapsed);
                },
                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    sessionStorage.setItem(CLAVE_TEMA_PLAYGROUND, this.theme);
                    sessionStorage.setItem(CLAVE_TEMA_MANUAL, '1');
                    this.applyTheme();
                },
                applyTheme() {
                    panelPlaygroundAplicarTema(this.$root, this.theme);
                },
            };
        }
    }
</script>
