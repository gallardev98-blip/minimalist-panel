<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

@once
    @push('panel-scripts')
        @include('panel::partials.chart-mount-runtime')
        <script>
            window.panelPlaygroundSincronizarGraficos = window.panelPlaygroundSincronizarGraficos || function () {
                if (typeof Chart === 'undefined') {
                    setTimeout(window.panelPlaygroundSincronizarGraficos, 40);
                    return;
                }

                window.panelRegisterPulsePlugin?.();
                document.querySelectorAll('.panel-playground-charts canvas[data-panel-chart-config]').forEach((canvas) => {
                    const raw = canvas.getAttribute('data-panel-chart-config');
                    if (!raw) return;

                    try {
                        const config = JSON.parse(raw);
                        window.panelChartConfigs[config.id] = config;
                        window.panelChartMount(config.id);
                    } catch (e) {
                        console.warn('panelPlaygroundSincronizarGraficos', e);
                    }
                });
            };

            document.addEventListener('livewire:init', () => {
                Livewire.hook('commit', ({ component, succeed }) => {
                    if (component.name !== 'panel.playground') return;

                    succeed(() => {
                        requestAnimationFrame(() => window.panelPlaygroundSincronizarGraficos?.());
                    });
                });

                Livewire.on('playground-tema-aplicado', () => window.panelPlaygroundSincronizarGraficos?.());
                Livewire.on('playground-graficos-actualizado', () => window.panelPlaygroundSincronizarGraficos?.());
                Livewire.on('playground-reiniciar-tema', () => setTimeout(() => window.panelPlaygroundSincronizarGraficos?.(), 80));
            });
        </script>
    @endpush
@endonce
