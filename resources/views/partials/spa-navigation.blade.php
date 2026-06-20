<script>
    (function () {
        function registerPanelSpaNavigation() {
            if (window.__panelSpaNavigationInitialized) {
                return;
            }

            window.__panelSpaNavigationInitialized = true;

            const MIN_VISIBLE_MS = 420;
            const EXIT_MS = 360;
            const VISIBLE_CLASS = 'panel-spa-loader--visible';
            const PROGRESS_CAP = 92;

            let shownAt = 0;
            let hideTimer = null;
            let exitTimer = null;
            let progressFrame = null;
            let progressStartedAt = 0;
            let isVisible = false;
            let lockFullscreenLoader = false;

            function loader() {
                return document.getElementById('panel-spa-loader');
            }

            function syncLoaderFullscreen(element) {
                if (! element) {
                    return;
                }

                const shouldUseFullscreen = lockFullscreenLoader
                    || element.classList.contains('panel-spa-loader--fullscreen')
                    || document.body.classList.contains('panel-auth-body');

                element.classList.toggle('panel-spa-loader--fullscreen', shouldUseFullscreen);
            }

            function main() {
                return document.getElementById('panel-main');
            }

            function progressLabel() {
                return document.querySelector('[data-panel-loader-progress]');
            }

            function progressBar() {
                return document.querySelector('[data-panel-loader-progressbar]');
            }

            function progressRing() {
                return document.querySelector('[data-panel-loader-ring]');
            }

            function lockScroll() {
                document.documentElement.classList.add('panel-scroll-lock');
                document.body.classList.add('panel-scroll-lock');
                window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
            }

            function unlockScroll() {
                document.documentElement.classList.remove('panel-scroll-lock');
                document.body.classList.remove('panel-scroll-lock');
            }

            function stopProgressAnimation() {
                if (progressFrame !== null) {
                    cancelAnimationFrame(progressFrame);
                    progressFrame = null;
                }
            }

            function setProgress(value) {
                const element = loader();
                const percent = Math.max(0, Math.min(100, Math.floor(value)));
                const label = progressLabel();
                const bar = progressBar();
                const ring = progressRing();

                if (label) {
                    label.textContent = `${percent}%`;
                }

                if (bar) {
                    bar.setAttribute('aria-valuenow', String(percent));
                }

                if (element) {
                    element.style.setProperty('--panel-loader-progress', String(percent));
                }

                if (ring) {
                    ring.style.setProperty('--panel-loader-progress', String(percent));
                }
            }

            function startProgress(isCached = false) {
                stopProgressAnimation();
                progressStartedAt = Date.now();
                setProgress(0);

                if (isCached) {
                    setProgress(100);

                    return;
                }

                const tick = () => {
                    if (! isVisible) {
                        return;
                    }

                    const elapsed = Date.now() - progressStartedAt;
                    const next = PROGRESS_CAP * (1 - Math.exp(-elapsed / 850));

                    setProgress(next);

                    if (next < PROGRESS_CAP) {
                        progressFrame = requestAnimationFrame(tick);
                    }
                };

                progressFrame = requestAnimationFrame(tick);
            }

            function finishProgress() {
                stopProgressAnimation();
                setProgress(100);
            }

            function showLoader(options = {}) {
                const element = loader();
                const mainEl = main();
                const { resetProgress = true, isCached = false } = options;

                if (! element) {
                    return;
                }

                clearTimeout(hideTimer);
                clearTimeout(exitTimer);

                if (! isVisible) {
                    shownAt = Date.now();
                    isVisible = true;

                    if (document.body.classList.contains('panel-auth-body')) {
                        lockFullscreenLoader = true;
                    }

                    syncLoaderFullscreen(element);
                    lockScroll();
                    element.setAttribute('aria-hidden', 'false');
                    element.setAttribute('aria-busy', 'true');
                    mainEl?.classList.add('panel-navigating');

                    requestAnimationFrame(() => {
                        element.classList.add(VISIBLE_CLASS);
                    });
                }

                if (resetProgress) {
                    startProgress(isCached);
                }
            }

            function hideLoader() {
                const element = loader();
                const mainEl = main();

                if (! element || ! isVisible) {
                    return;
                }

                finishProgress();

                const elapsed = Date.now() - shownAt;
                const delay = Math.max(0, MIN_VISIBLE_MS - elapsed);

                clearTimeout(hideTimer);
                clearTimeout(exitTimer);

                hideTimer = setTimeout(() => {
                    element.classList.remove(VISIBLE_CLASS);
                    element.setAttribute('aria-busy', 'false');

                    exitTimer = setTimeout(() => {
                        element.setAttribute('aria-hidden', 'true');
                        mainEl?.classList.remove('panel-navigating');
                        unlockScroll();
                        isVisible = false;
                        lockFullscreenLoader = false;
                        syncLoaderFullscreen(element);
                        stopProgressAnimation();
                        setProgress(0);
                    }, EXIT_MS);
                }, delay);
            }

            function resolveNavigatePath(detail) {
                try {
                    const raw = detail?.url ?? window.location.href;

                    return new URL(String(raw), window.location.origin).pathname;
                } catch {
                    return window.location.pathname;
                }
            }

            function isPanelAuthPath(pathname) {
                const authSegments = ['/login', '/register', '/forgot-password', '/reset-password'];

                return authSegments.some((segment) => pathname.includes(segment));
            }

            function cleanupLayoutArtifacts() {
                if (! document.querySelector('.panel-shell')) {
                    return;
                }

                document.documentElement.classList.remove('panel-auth-root');

                document.querySelectorAll('.panel-auth-bg, .panel-auth-shell, .panel-auth-theme-toggle').forEach((element) => {
                    element.remove();
                });
            }

            document.addEventListener('livewire:navigate', (event) => {
                if (isPanelAuthPath(resolveNavigatePath(event.detail))) {
                    return;
                }

                showLoader({
                    resetProgress: true,
                    isCached: Boolean(event.detail?.cached),
                });
            });

            document.addEventListener('livewire:navigating', (event) => {
                if (isPanelAuthPath(resolveNavigatePath(event.detail))) {
                    return;
                }

                showLoader({ resetProgress: false });
            });

            document.addEventListener('livewire:navigated', () => {
                hideLoader();
                cleanupLayoutArtifacts();
                window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
            });
        }

        if (window.Livewire) {
            registerPanelSpaNavigation();
        } else {
            document.addEventListener('livewire:init', registerPanelSpaNavigation);
        }
    })();
</script>
