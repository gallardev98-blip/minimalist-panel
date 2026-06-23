<div class="panel-auth-form panel-auth-form--login">
    <div class="panel-auth-intro">
        <h1 class="panel-auth-title">{{ __('panel::panel.auth.login_title') }}</h1>
        <p class="panel-auth-subtitle">{{ __('panel::panel.auth.login_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="login" class="panel-auth-fields" novalidate>
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
            'autocomplete' => 'current-password',
        ])

        @if ($passwordResetEnabled)
            <div class="panel-auth-forgot">
                <a href="{{ panel_route('password.request') }}" class="panel-auth-forgot-link" wire:navigate>
                    {{ __('panel::panel.auth.forgot_password') }}
                </a>
            </div>
        @endif

        <label class="panel-auth-remember">
            <input type="checkbox" wire:model="remember" class="panel-checkbox panel-auth-checkbox" />
            <span>{{ __('panel::panel.auth.remember') }}</span>
        </label>

        <button
            type="submit"
            class="panel-btn panel-btn-primary panel-auth-submit"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="login">{{ __('panel::panel.auth.login_action') }}</span>
            <span wire:loading wire:target="login">
                @include('panel::partials.auth-loading-text', ['label' => __('panel::panel.auth.logging_in')])
            </span>
        </button>
    </form>

    @if ($registrationEnabled)
        <div class="panel-auth-footer">
            <span class="panel-auth-footer-text">{{ __('panel::panel.auth.no_account') }}</span>
            <a href="{{ panel_route('register') }}" class="panel-auth-footer-link" wire:navigate>
                {{ __('panel::panel.auth.register_action') }}
            </a>
        </div>
    @endif
</div>
