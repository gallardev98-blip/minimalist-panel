<div class="panel-auth-form panel-auth-form--reset">
    <div class="panel-auth-intro">
        <h1 class="panel-auth-title">{{ __('panel::panel.auth.reset_title') }}</h1>
        <p class="panel-auth-subtitle">{{ __('panel::panel.auth.reset_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="resetPassword" class="panel-auth-fields" novalidate>
        @include('panel::partials.auth-field', [
            'name' => 'email',
            'label' => __('panel::panel.auth.email'),
            'type' => 'email',
            'icon' => 'mail',
            'autocomplete' => 'username',
            'autofocus' => true,
        ])

        @include('panel::partials.auth-field', [
            'name' => 'password',
            'label' => __('panel::panel.auth.password'),
            'type' => 'password',
            'icon' => 'lock',
            'autocomplete' => 'new-password',
        ])

        @include('panel::partials.auth-field', [
            'name' => 'password_confirmation',
            'label' => __('panel::panel.auth.password_confirm'),
            'type' => 'password',
            'icon' => 'shield',
            'autocomplete' => 'new-password',
        ])

        <button
            type="submit"
            class="panel-btn panel-btn-primary panel-auth-submit"
            wire:loading.attr="disabled"
            wire:loading.class="panel-auth-submit--loading"
        >
            <span wire:loading.remove wire:target="resetPassword">{{ __('panel::panel.auth.reset_action') }}</span>
            <span wire:loading wire:target="resetPassword" class="panel-auth-submit-loading" aria-hidden="true">
                <x-panel::icon name="loader-2" class="h-4 w-4 animate-spin" />
            </span>
            <span wire:loading wire:target="resetPassword" class="sr-only">{{ __('panel::panel.loading') }}</span>
        </button>
    </form>

    <div class="panel-auth-footer">
        <a href="{{ panel_route('login') }}" class="panel-auth-footer-link" wire:navigate>
            {{ __('panel::panel.auth.back_to_login') }}
        </a>
    </div>
</div>
