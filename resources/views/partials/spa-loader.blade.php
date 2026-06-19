<div
    id="panel-spa-loader"
    class="panel-spa-loader"
    aria-live="polite"
    aria-hidden="true"
    aria-busy="false"
    aria-label="{{ __('panel::panel.loading_page') }}"
>
    <div class="panel-spa-loader-backdrop" aria-hidden="true"></div>
    <div class="panel-spa-loader-content">
        <div
            class="panel-spa-loader-ring"
            role="progressbar"
            aria-valuemin="0"
            aria-valuemax="100"
            aria-valuenow="0"
            data-panel-loader-progressbar
        >
            <span class="panel-spa-loader-ring-track" aria-hidden="true"></span>
            <span class="panel-spa-loader-ring-progress" data-panel-loader-ring aria-hidden="true"></span>
            <span class="panel-spa-loader-percent" data-panel-loader-progress aria-hidden="true">0%</span>
        </div>
        <p class="panel-spa-loader-title">{{ __('panel::panel.loading') }}</p>
    </div>
</div>
