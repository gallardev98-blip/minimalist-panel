<div class="panel-auth-form panel-auth-form--register">
    <div class="panel-auth-intro">
        <h1 class="panel-auth-title">{{ __('panel::panel.auth.register_title') }}</h1>
        <p class="panel-auth-subtitle">{{ __('panel::panel.auth.register_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="register" class="panel-auth-fields" novalidate>
        @include('panel::partials.auth-field', [
            'name' => 'name',
            'label' => __('panel::panel.auth.name'),
            'type' => 'text',
            'icon' => 'user',
            'autocomplete' => 'name',
            'autofocus' => true,
        ])

        @include('panel::partials.auth-field', [
            'name' => 'email',
            'label' => __('panel::panel.auth.email'),
            'type' => 'email',
            'icon' => 'mail',
            'autocomplete' => 'username',
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
        >
            <span wire:loading.remove wire:target="register">{{ __('panel::panel.auth.register_action') }}</span>
            <span wire:loading wire:target="register">
                @include('panel::partials.auth-loading-text', ['label' => __('panel::panel.auth.registering')])
            </span>
        </button>
    </form>

    <div class="panel-auth-footer">
        <span class="panel-auth-footer-text">{{ __('panel::panel.auth.has_account') }}</span>
        <a href="{{ route('panel.login') }}" class="panel-auth-footer-link" wire:navigate>
            {{ __('panel::panel.auth.login_action') }}
        </a>
    </div>
</div>
