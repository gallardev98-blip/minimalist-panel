@php
    $spaLoaderActivo = (bool) config('panel.layout.spa_loader', true);
    $spaLoaderTimeoutMs = max(3000, (int) config('panel.layout.spa_loader_timeout_ms', 20000));
    $spaLoaderEscape = (bool) config('panel.layout.spa_loader_escape', true);
@endphp

@if ($spaLoaderActivo)
<script>
    (function () {
        function registerPanelSpaNavigation() {
            if (window.__panelSpaNavigationInitialized) {
                return;
            }

            window.__panelSpaNavigationInitialized = true;

            const MIN_VISIBLE_MS = 280;
            const AUTH_MIN_VISIBLE_MS = 120;
            const EXIT_MS = 280;
            const AUTH_EXIT_MS = 200;
            const VISIBLE_CLASS = 'panel-spa-loader--visible';
            const PROGRESS_CAP = 92;
            const WATCHDOG_MS = {{ $spaLoaderTimeoutMs }};
            const ESCAPE_DISMISS = @json($spaLoaderEscape);

            let shownAt = 0;
            let hideTimer = null;
            let exitTimer = null;
            let watchdogTimer = null;
            let progressFrame = null;
            let progressStartedAt = 0;
            let isVisible = false;
            let lockFullscreenLoader = false;
            let isAuthTransition = false;

            function loader() {
                return document.getElementById('panel-spa-loader');
            }

            function syncLoaderFullscreen(element) {
                if (! element) {
                    return;
                }

                const shouldUseFullscreen = lockFullscreenLoader
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

            function clearWatchdog() {
                if (watchdogTimer !== null) {
                    clearTimeout(watchdogTimer);
                    watchdogTimer = null;
                }
            }

            function startWatchdog() {
                clearWatchdog();

                watchdogTimer = setTimeout(() => {
                    if (isVisible) {
                        forceHideLoader();
                    }
                }, WATCHDOG_MS);
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

            function resetLoaderState(element, mainEl) {
                clearWatchdog();
                clearTimeout(hideTimer);
                clearTimeout(exitTimer);
                stopProgressAnimation();

                element?.classList.remove(VISIBLE_CLASS);
                element?.setAttribute('aria-busy', 'false');
                element?.setAttribute('aria-hidden', 'true');
                mainEl?.classList.remove('panel-navigating');
                unlockScroll();
                isVisible = false;
                lockFullscreenLoader = false;
                isAuthTransition = false;
                syncLoaderFullscreen(element);
                setProgress(0);
            }

            function forceHideLoader() {
                resetLoaderState(loader(), main());
            }

            function showLoader(options = {}) {
                const element = loader();
                const mainEl = main();
                const { resetProgress = true, isCached = false } = options;

                if (! element) {
                    return;
                }

                if (isVisible && ! resetProgress) {
                    return;
                }

                clearTimeout(hideTimer);
                clearTimeout(exitTimer);

                if (! isVisible) {
                    shownAt = Date.now();
                    isVisible = true;

                    if (document.body.classList.contains('panel-auth-body')) {
                        lockFullscreenLoader = true;
                        isAuthTransition = true;
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

                startWatchdog();
            }

            function hideLoader() {
                const element = loader();
                const mainEl = main();

                if (! element || ! isVisible) {
                    clearWatchdog();

                    return;
                }

                clearWatchdog();
                finishProgress();

                const minVisible = isAuthTransition ? AUTH_MIN_VISIBLE_MS : MIN_VISIBLE_MS;
                const exitMs = isAuthTransition ? AUTH_EXIT_MS : EXIT_MS;
                const elapsed = Date.now() - shownAt;
                const delay = Math.max(0, minVisible - elapsed);

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
                        isAuthTransition = false;
                        syncLoaderFullscreen(element);
                        stopProgressAnimation();
                        setProgress(0);
                    }, exitMs);
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
                const authSegments = ['/login', '/register', '/forgot-password', '/reset-password', '/email/verify'];

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

                document.body.classList.remove('panel-auth-body');
                syncLoaderFullscreen(loader());
            }

            document.addEventListener('livewire:navigate', (event) => {
                if (event.defaultPrevented) {
                    return;
                }

                if (isPanelAuthPath(resolveNavigatePath(event.detail))) {
                    return;
                }

                showLoader({
                    resetProgress: true,
                    isCached: Boolean(event.detail?.cached),
                });
            });

            document.addEventListener('livewire:navigated', () => {
                hideLoader();
                cleanupLayoutArtifacts();
                window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
            });

            window.addEventListener('pageshow', () => {
                if (isVisible) {
                    forceHideLoader();
                }
            });

            if (ESCAPE_DISMISS) {
                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape' || ! isVisible) {
                        return;
                    }

                    event.preventDefault();
                    forceHideLoader();
                });
            }

            window.panelSpaLoader = {
                ocultar: forceHideLoader,
                estaVisible: () => isVisible,
            };
        }

        if (window.Livewire) {
            registerPanelSpaNavigation();
        } else {
            document.addEventListener('livewire:init', registerPanelSpaNavigation);
        }
    })();
</script>
@endif
