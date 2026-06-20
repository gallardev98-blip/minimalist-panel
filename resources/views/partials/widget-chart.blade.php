@php
    $chartId = 'panel-chart-' . md5($widget->getLabel() . spl_object_id($widget));
    $chartConfig = [
        'id' => $chartId,
        'label' => $widget->getLabel(),
        'type' => $widget->resolveChartType(),
        'isProgression' => $widget->isProgression(),
        'labels' => $widget->getChartData()['labels'] ?? [],
        'values' => $widget->getChartData()['values'] ?? [],
        'colors' => $widget->getColors(),
        'colorKeys' => $widget->getThemeColorKeys(),
        'options' => $widget->getChartOptions(),
    ];
@endphp

<div class="panel-chart-wrap {{ $widget->isProgression() ? 'panel-chart-wrap--progression' : '' }}" style="height: {{ $widget->getHeight() }}px">
    <canvas id="{{ $chartId }}" data-panel-chart aria-label="{{ $widget->getLabel() }}"></canvas>
</div>

@once
    @push('panel-scripts')
        <script>
            window.panelChartConfigs = window.panelChartConfigs || {};
            window.panelChartTheme = window.panelChartTheme || function () {
                const root = getComputedStyle(document.documentElement);
                const pick = (name) => {
                    const raw = root.getPropertyValue(name).trim();
                    return raw ? `rgb(${raw})` : null;
                };
                return {
                    primary: pick('--panel-primary') || '#171717',
                    muted: pick('--panel-muted') || '#737373',
                    border: pick('--panel-border') || '#e5e5e5',
                    card: pick('--panel-card') || '#ffffff',
                    isDark: document.documentElement.classList.contains('dark'),
                };
            };
            window.panelChartColorKey = window.panelChartColorKey || function (key) {
                const vars = {
                    primary: '--panel-primary',
                    accent: '--panel-accent',
                    success: '--panel-success',
                    danger: '--panel-danger',
                    warning: '--panel-warning',
                    muted: '--panel-muted',
                };
                const root = getComputedStyle(document.documentElement);
                const raw = root.getPropertyValue(vars[key] || vars.primary).trim();
                return raw ? `rgb(${raw})` : window.panelChartTheme().primary;
            };
            window.panelChartWithAlpha = window.panelChartWithAlpha || function (rgb, alpha) {
                const parts = rgb.match(/\d+/g);
                if (!parts || parts.length < 3) return rgb;
                return `rgba(${parts[0]}, ${parts[1]}, ${parts[2]}, ${alpha})`;
            };
            window.panelChartResolvePalette = window.panelChartResolvePalette || function (count, manualColors, colorKeys) {
                if (manualColors?.length) return manualColors;
                if (colorKeys?.length) {
                    return Array.from({ length: count }, (_, i) => window.panelChartColorKey(colorKeys[i % colorKeys.length]));
                }
                return window.panelChartPalette(count, []);
            };
            window.panelChartPalette = window.panelChartPalette || function (count, custom) {
                if (custom?.length) return custom;
                const t = window.panelChartTheme();
                return Array.from({ length: count }, (_, i) => [t.primary, t.muted, t.border][i % 3]);
            };
            window.panelRegisterPulsePlugin = window.panelRegisterPulsePlugin || function () {
                if (typeof Chart === 'undefined' || window.panelPulseRegistered) return;
                window.panelPulseRegistered = true;
                Chart.register({
                    id: 'panelPulse',
                    afterDraw(chart) {
                        if (!chart.options.panelProgression) return;
                        const meta = chart.getDatasetMeta(0);
                        const theme = window.panelChartTheme();
                        const ctx = chart.ctx;
                        const isDark = theme.isDark;
                        meta.data.forEach((pt, i) => {
                            if (!pt || pt.skip) return;
                            const { x, y } = pt.getProps(['x', 'y'], true);
                            const pulse = 0.5 + 0.5 * Math.sin(Date.now() / 380 + i * 0.9);
                            const halo = 3 + pulse * (isDark ? 4 : 6);
                            ctx.save();
                            ctx.fillStyle = theme.primary;
                            ctx.globalAlpha = isDark ? 0.06 + pulse * 0.14 : 0.12 + pulse * 0.28;
                            ctx.beginPath();
                            ctx.arc(x, y, halo, 0, Math.PI * 2);
                            ctx.fill();
                            ctx.globalAlpha = 1;
                            ctx.beginPath();
                            ctx.arc(x, y, isDark ? 3 : 3.5, 0, Math.PI * 2);
                            ctx.fill();
                            ctx.restore();
                        });
                        if (!chart._panelPulseFrame) {
                            const loop = () => {
                                if (!chart.canvas?.isConnected) {
                                    chart._panelPulseFrame = null;
                                    return;
                                }
                                chart.draw();
                                chart._panelPulseFrame = requestAnimationFrame(loop);
                            };
                            chart._panelPulseFrame = requestAnimationFrame(loop);
                        }
                    },
                    beforeDestroy(chart) {
                        if (chart._panelPulseFrame) cancelAnimationFrame(chart._panelPulseFrame);
                        chart._panelPulseFrame = null;
                    },
                });
            };
            window.panelChartDestroy = window.panelChartDestroy || function (canvas) {
                if (!canvas || typeof Chart === 'undefined') return;
                const existing = Chart.getChart(canvas);
                if (!existing) return;
                if (existing._panelPulseFrame) cancelAnimationFrame(existing._panelPulseFrame);
                existing.destroy();
            };
            window.panelChartMount = window.panelChartMount || function (chartId) {
                if (typeof Chart === 'undefined') return false;
                window.panelRegisterPulsePlugin?.();
                const config = window.panelChartConfigs[chartId];
                const canvas = document.getElementById(chartId);
                if (!config || !canvas) return false;
                window.panelChartDestroy(canvas);

                const theme = window.panelChartTheme();
                let type = config.type;
                if (type === 'progression') type = 'line';
                const isProgression = !!config.isProgression;
                const labels = config.labels || [];
                const values = config.values || [];
                const customColors = config.colors || [];
                const colorKeys = config.colorKeys || null;
                const customOptions = config.options || {};
                const isCircular = type === 'pie' || type === 'doughnut';
                const isLine = type === 'line';
                const palette = window.panelChartResolvePalette(values.length, customColors, colorKeys);
                const lineColor = isLine
                    ? (colorKeys?.length ? window.panelChartColorKey(colorKeys[0]) : theme.primary)
                    : theme.primary;

                const dataset = {
                    data: values,
                    borderWidth: isLine ? 2.5 : 0,
                    borderRadius: type === 'bar' ? 8 : 0,
                    borderSkipped: false,
                    tension: isProgression ? 0.42 : 0.35,
                    fill: isLine,
                    pointRadius: isProgression ? 0 : (isLine ? 0 : undefined),
                    pointHoverRadius: isLine ? 6 : undefined,
                    backgroundColor: isCircular ? palette : (isLine ? window.panelChartWithAlpha(lineColor, theme.isDark ? 0.12 : 0.14) : palette.map(c => `${c}bf`)),
                    borderColor: isLine ? lineColor : palette,
                };

                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    panelProgression: isProgression,
                    animation: { duration: isProgression ? 900 : 650, easing: 'easeOutQuart' },
                    plugins: {
                        legend: {
                            display: isCircular,
                            position: 'bottom',
                            labels: { color: theme.muted, boxWidth: 10, usePointStyle: true, padding: 14 },
                        },
                        tooltip: {
                            backgroundColor: theme.card,
                            titleColor: theme.primary,
                            bodyColor: theme.muted,
                            borderColor: theme.border,
                            borderWidth: 1,
                            padding: 10,
                        },
                    },
                };

                if (isCircular) {
                    options.scales = { x: { display: false }, y: { display: false } };
                    if (type === 'doughnut') options.cutout = '68%';
                } else {
                    options.scales = {
                        x: { grid: { display: false }, ticks: { color: theme.muted, font: { size: 11 } }, border: { display: false } },
                        y: {
                            beginAtZero: !isProgression,
                            grid: { color: `${theme.border}66`, drawBorder: false },
                            ticks: { color: theme.muted, font: { size: 11 }, maxTicksLimit: isProgression ? 5 : 8 },
                            border: { display: false },
                        },
                    };
                }

                Object.assign(options, customOptions);
                if (isCircular) options.scales = { x: { display: false }, y: { display: false } };

                new Chart(canvas, { type, data: { labels, datasets: [dataset] }, options });
                return true;
            };
            window.panelChartRefreshAll = window.panelChartRefreshAll || function () {
                if (typeof Chart === 'undefined') return;
                Object.keys(window.panelChartConfigs).forEach((id) => {
                    if (document.getElementById(id)) window.panelChartMount(id);
                });
            };
            window.panelChartInitPending = window.panelChartInitPending || function () {
                const tryInit = () => {
                    if (typeof Chart === 'undefined') {
                        setTimeout(tryInit, 40);
                        return;
                    }
                    window.panelChartRefreshAll();
                };
                tryInit();
            };
            if (!window.panelChartListenersReady) {
                window.panelChartListenersReady = true;
                document.addEventListener('DOMContentLoaded', () => window.panelChartInitPending());
                document.addEventListener('livewire:navigated', () => window.panelChartInitPending());
                document.addEventListener('panel-theme-changed', () => window.panelChartRefreshAll());
            }
        </script>
    @endpush
@endonce

@push('panel-scripts')
    <script>
        window.panelChartConfigs[@json($chartId)] = @json($chartConfig);
        window.panelChartInitPending?.();
    </script>
@endpush
