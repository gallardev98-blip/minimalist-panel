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
        <div class="panel-spa-loader-ring" aria-hidden="true">
            <span class="panel-spa-loader-ring-track"></span>
            <span class="panel-spa-loader-ring-spinner"></span>
        </div>
        <p class="panel-spa-loader-title">{{ __('panel::panel.loading') }}</p>
    </div>
</div>
