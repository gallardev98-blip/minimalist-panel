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

            let shownAt = 0;
            let hideTimer = null;
            let exitTimer = null;

            function loader() {
                return document.getElementById('panel-spa-loader');
            }

            function main() {
                return document.getElementById('panel-main');
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

            function showLoader() {
                const element = loader();
                const mainEl = main();

                if (! element) {
                    return;
                }

                clearTimeout(hideTimer);
                clearTimeout(exitTimer);

                shownAt = Date.now();
                lockScroll();
                element.setAttribute('aria-hidden', 'false');
                element.setAttribute('aria-busy', 'true');
                mainEl?.classList.add('panel-navigating');

                requestAnimationFrame(() => {
                    element.classList.add(VISIBLE_CLASS);
                });
            }

            function hideLoader() {
                const element = loader();
                const mainEl = main();

                if (! element) {
                    return;
                }

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
                    }, EXIT_MS);
                }, delay);
            }

            function cleanupLayoutArtifacts() {
                document.documentElement.classList.remove('panel-auth-root');

                document.querySelectorAll('.panel-auth-bg, .panel-auth-shell, .panel-auth-theme-toggle').forEach((element) => {
                    element.remove();
                });

                document.querySelectorAll('[x-persist="panel-header"]').forEach((element) => element.remove());

                const headers = document.querySelectorAll('.panel-shell > .panel-header');
                headers.forEach((header, index) => {
                    if (index > 0) {
                        header.remove();
                    }
                });
            }

            document.addEventListener('livewire:navigate', showLoader);
            document.addEventListener('livewire:navigating', showLoader);
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
