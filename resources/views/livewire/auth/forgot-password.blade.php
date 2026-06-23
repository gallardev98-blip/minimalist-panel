<div class="panel-auth-form panel-auth-form--forgot">
    <div class="panel-auth-intro">
        <h1 class="panel-auth-title">{{ __('panel::panel.auth.forgot_title') }}</h1>
        <p class="panel-auth-subtitle">{{ __('panel::panel.auth.forgot_subtitle') }}</p>
    </div>

    @if ($statusMessage)
        <div class="panel-auth-status" role="status">
            {{ $statusMessage }}
        </div>
    @endif

    <form wire:submit.prevent="sendResetLink" class="panel-auth-fields" novalidate>
        @include('panel::partials.auth-field', [
            'name' => 'email',
            'label' => __('panel::panel.auth.email'),
            'type' => 'email',
            'icon' => 'mail',
            'autocomplete' => 'username',
            'autofocus' => true,
        ])

        <button
            type="submit"
            class="panel-btn panel-btn-primary panel-auth-submit"
            wire:loading.attr="disabled"
            wire:loading.class="panel-auth-submit--loading"
        >
            <span wire:loading.remove wire:target="sendResetLink">{{ __('panel::panel.auth.send_reset_link') }}</span>
            <span wire:loading wire:target="sendResetLink" class="panel-auth-submit-loading" aria-hidden="true">
                <x-panel::icon name="loader-2" class="h-4 w-4 animate-spin" />
            </span>
            <span wire:loading wire:target="sendResetLink" class="sr-only">{{ __('panel::panel.loading') }}</span>
        </button>
    </form>

    <div class="panel-auth-footer">
        <a href="{{ panel_route('login') }}" class="panel-auth-footer-link" wire:navigate>
            {{ __('panel::panel.auth.back_to_login') }}
        </a>
    </div>
</div>
