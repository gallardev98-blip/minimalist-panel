<div>
    <div class="panel-auth-icon mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full" style="background: rgb(var(--panel-elevated));">
        <x-panel::icon name="mail" class="h-6 w-6" />
    </div>

    <h1 class="panel-heading text-center text-xl font-bold">{{ __('panel::panel.auth.verification.title') }}</h1>
    <p class="panel-muted mt-2 text-center text-sm">{{ __('panel::panel.auth.verification.subtitle') }}</p>

    @if (session('status'))
        <p class="panel-success mt-4 text-center text-sm">{{ session('status') }}</p>
    @endif

    <div class="mt-6 flex flex-col gap-3">
        <button type="button" wire:click="sendVerification" class="panel-btn panel-btn-primary w-full">
            {{ __('panel::panel.auth.verification.resend') }}
        </button>

        <button type="button" wire:click="logout" class="panel-btn panel-btn-ghost w-full">
            {{ __('panel::panel.logout') }}
        </button>
    </div>
</div>
