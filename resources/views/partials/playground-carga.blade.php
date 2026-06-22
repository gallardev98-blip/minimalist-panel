@include('panel::partials.spa-loader', ['fullscreen' => true])

<script>
    (function () {
        if (window.panelPlaygroundCarga) {
            return;
        }

        const VISIBLE = 'panel-spa-loader--visible';
        const MIN_MS = 220;
        const EXIT_MS = 220;
        let visible = false;
        let shownAt = 0;
        let hideTimer = null;
        let exitTimer = null;
        let progressFrame = null;
        let progressAt = 0;

        function loader() {
            return document.getElementById('panel-spa-loader');
        }

        function label() {
            return document.querySelector('[data-panel-loader-progress]');
        }

        function ring() {
            return document.querySelector('[data-panel-loader-ring]');
        }

        function setProgress(value) {
            const percent = Math.max(0, Math.min(100, Math.floor(value)));
            label() && (label().textContent = `${percent}%`);
            const el = loader();
            el?.style.setProperty('--panel-loader-progress', String(percent));
            ring()?.style.setProperty('--panel-loader-progress', String(percent));
        }

        function stopProgress() {
            if (progressFrame !== null) {
                cancelAnimationFrame(progressFrame);
                progressFrame = null;
            }
        }

        function startProgress() {
            stopProgress();
            progressAt = Date.now();
            setProgress(0);

            const tick = () => {
                if (!visible) {
                    return;
                }

                const elapsed = Date.now() - progressAt;
                const next = 92 * (1 - Math.exp(-elapsed / 700));
                setProgress(next);

                if (next < 92) {
                    progressFrame = requestAnimationFrame(tick);
                }
            };

            progressFrame = requestAnimationFrame(tick);
        }

        function mostrar() {
            const el = loader();
            if (!el || visible) {
                return;
            }

            clearTimeout(hideTimer);
            clearTimeout(exitTimer);
            visible = true;
            shownAt = Date.now();
            el.classList.add('panel-spa-loader--fullscreen');
            el.setAttribute('aria-hidden', 'false');
            el.setAttribute('aria-busy', 'true');
            requestAnimationFrame(() => el.classList.add(VISIBLE));
            startProgress();
        }

        function ocultar() {
            const el = loader();
            if (!el || !visible) {
                return;
            }

            stopProgress();
            setProgress(100);

            const delay = Math.max(0, MIN_MS - (Date.now() - shownAt));
            clearTimeout(hideTimer);
            clearTimeout(exitTimer);

            hideTimer = setTimeout(() => {
                el.classList.remove(VISIBLE);
                el.setAttribute('aria-busy', 'false');

                exitTimer = setTimeout(() => {
                    el.setAttribute('aria-hidden', 'true');
                    visible = false;
                    stopProgress();
                    setProgress(0);
                }, EXIT_MS);
            }, delay);
        }

        window.panelPlaygroundCarga = { mostrar, ocultar };

        document.addEventListener('livewire:init', () => {
            Livewire.on('playground-tema-aplicado', () => ocultar());
            Livewire.on('playground-mostrar-carga', () => mostrar());
        });
    })();
</script>
