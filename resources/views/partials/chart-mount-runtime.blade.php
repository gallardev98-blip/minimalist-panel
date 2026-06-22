<script>
    window.panelChartConfigs = window.panelChartConfigs || {};
    window.panelChartTheme = window.panelChartTheme || function () {
        const raizPlayground = document.querySelector('.panel-playground-root');
        const estilos = getComputedStyle(raizPlayground || document.documentElement);
        const escenario = document.querySelector('.panel-playground-escenario');
        const pick = (nombre) => {
            const raw = estilos.getPropertyValue(nombre).trim();
            return raw ? `rgb(${raw})` : null;
        };

        return {
            primary: pick('--panel-primary') || '#171717',
            muted: pick('--panel-muted') || '#737373',
            border: pick('--panel-border') || '#e5e5e5',
            card: pick('--panel-card') || '#ffffff',
            isDark: escenario
                ? escenario.classList.contains('dark')
                : document.documentElement.classList.contains('dark'),
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
        const raiz = document.querySelector('.panel-playground-root') || document.documentElement;
        const raw = getComputedStyle(raiz).getPropertyValue(vars[key] || vars.primary).trim();

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
        const keys = ['primary', 'accent', 'success', 'warning', 'danger'];
        return Array.from({ length: count }, (_, i) => window.panelChartColorKey(keys[i % keys.length]));
    };
    window.panelChartLineGradient = window.panelChartLineGradient || function (context, color, theme) {
        const chart = context.chart;
        const { ctx, chartArea } = chart;
        if (!chartArea) return window.panelChartWithAlpha(color, theme.isDark ? 0.14 : 0.18);
        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        gradient.addColorStop(0, window.panelChartWithAlpha(color, theme.isDark ? 0.32 : 0.28));
        gradient.addColorStop(0.55, window.panelChartWithAlpha(color, theme.isDark ? 0.1 : 0.12));
        gradient.addColorStop(1, window.panelChartWithAlpha(color, 0));
        return gradient;
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
        const style = customOptions.panelStyle || {};
        const chartJsOptions = { ...customOptions };
        delete chartJsOptions.panelStyle;

        const isCircular = type === 'pie' || type === 'doughnut';
        const isLine = type === 'line';
        const isBar = type === 'bar';
        const palette = window.panelChartResolvePalette(values.length, customColors, colorKeys);
        const lineColor = isLine
            ? (colorKeys?.length ? window.panelChartColorKey(colorKeys[0]) : theme.primary)
            : theme.primary;
        const borderRadius = style.borderRadius ?? (isBar ? 12 : 0);
        const useGradient = style.gradient !== false;
        const animate = style.animation !== false;
        const showLegend = style.legend !== false;
        const cutout = style.cutout ?? '72%';
        const barPct = style.barPercentage ?? 0.62;
        const catPct = style.categoryPercentage ?? 0.78;

        const barFill = (color) => {
            if (!useGradient) return window.panelChartWithAlpha(color, theme.isDark ? 0.88 : 0.82);
            return window.panelChartWithAlpha(color, theme.isDark ? 0.92 : 0.88);
        };

        const dataset = {
            data: values,
            borderWidth: isLine ? 2.75 : (isCircular ? 2 : 0),
            borderRadius: isBar ? borderRadius : 0,
            borderSkipped: false,
            barPercentage: isBar ? barPct : undefined,
            categoryPercentage: isBar ? catPct : undefined,
            spacing: isCircular ? (style.preset === 'bold' ? 5 : 3) : undefined,
            tension: isProgression ? 0.45 : 0.38,
            fill: isLine,
            pointRadius: isProgression ? 0 : (isLine ? 3 : undefined),
            pointHoverRadius: isLine ? 7 : undefined,
            pointBackgroundColor: isLine ? lineColor : undefined,
            pointBorderColor: isLine ? theme.card : undefined,
            pointBorderWidth: isLine ? 2 : undefined,
            backgroundColor: isCircular
                ? palette.map((c, i) => window.panelChartWithAlpha(c, theme.isDark ? 0.92 : 0.88))
                : (isLine
                    ? (useGradient
                        ? (ctx) => window.panelChartLineGradient(ctx, lineColor, theme)
                        : window.panelChartWithAlpha(lineColor, theme.isDark ? 0.12 : 0.14))
                    : palette.map((c) => barFill(c))),
            borderColor: isLine ? lineColor : palette,
            hoverBackgroundColor: isBar ? palette.map((c) => window.panelChartWithAlpha(c, 1)) : undefined,
            hoverBorderColor: isCircular ? theme.card : undefined,
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            panelProgression: isProgression,
            interaction: { mode: 'index', intersect: false },
            animation: animate ? { duration: isProgression ? 950 : 780, easing: 'easeOutQuart' } : false,
            layout: { padding: { top: 8, right: 4, bottom: 4, left: 4 } },
            plugins: {
                legend: {
                    display: showLegend && isCircular,
                    position: 'bottom',
                    labels: {
                        color: theme.muted,
                        boxWidth: 8,
                        boxHeight: 8,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { size: 11, weight: '500' },
                    },
                },
                tooltip: {
                    backgroundColor: theme.card,
                    titleColor: theme.primary,
                    bodyColor: theme.muted,
                    borderColor: theme.border,
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    boxPadding: 6,
                },
            },
        };

        if (isCircular) {
            options.scales = { x: { display: false }, y: { display: false } };
            if (type === 'doughnut') options.cutout = cutout;
        } else {
            options.scales = {
                x: {
                    grid: { display: false },
                    ticks: { color: theme.muted, font: { size: 11, weight: '500' }, padding: 6 },
                    border: { display: false },
                },
                y: {
                    beginAtZero: !isProgression,
                    grid: { color: `${theme.border}55`, drawBorder: false, tickLength: 0 },
                    ticks: { color: theme.muted, font: { size: 11 }, maxTicksLimit: isProgression ? 5 : 6, padding: 8 },
                    border: { display: false },
                },
            };
        }

        Object.assign(options, chartJsOptions);
        if (isCircular) options.scales = { x: { display: false }, y: { display: false } };
        if (type === 'doughnut' && chartJsOptions.cutout) options.cutout = chartJsOptions.cutout;

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
            window.panelPlaygroundSincronizarGraficos?.();
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
