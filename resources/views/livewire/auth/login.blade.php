<div class="panel-auth-form panel-auth-form--login">
    <div class="panel-auth-intro">
        <h1 class="panel-auth-title">{{ __('panel::panel.auth.login_title') }}</h1>
        <p class="panel-auth-subtitle">{{ __('panel::panel.auth.login_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="login" class="panel-auth-fields" novalidate>
        @if ($errors->any())
            <div class="panel-auth-error panel-auth-error-summary" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

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
                <a href="{{ route('panel.password.request') }}" class="panel-auth-forgot-link" wire:navigate>
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
            wire:loading.class="panel-auth-submit--loading"
        >
            <span wire:loading.remove wire:target="login">{{ __('panel::panel.auth.login_action') }}</span>
            <span wire:loading wire:target="login" class="panel-auth-submit-loading" aria-hidden="true">
                <x-panel::icon name="loader-2" class="h-4 w-4 animate-spin" />
            </span>
            <span wire:loading wire:target="login" class="sr-only">{{ __('panel::panel.loading') }}</span>
        </button>
    </form>

    @if ($registrationEnabled)
        <div class="panel-auth-footer">
            <span class="panel-auth-footer-text">{{ __('panel::panel.auth.no_account') }}</span>
            <a href="{{ route('panel.register') }}" class="panel-auth-footer-link" wire:navigate>
                {{ __('panel::panel.auth.register_action') }}
            </a>
        </div>
    @endif
</div>
