<div>
    <div class="panel-page-hero">
        <h1>{{ __('panel::panel.profile.title') }}</h1>
        <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.profile.subtitle') }}</p>
    </div>

    <form wire:submit="save" class="panel-form-card">
        <div class="panel-form-card-body space-y-6">
            <section class="space-y-4">
                <h2 class="panel-profile-section-title">{{ __('panel::panel.profile.account_section') }}</h2>

                @include('panel::partials.profile-field', [
                    'name' => 'name',
                    'label' => __('panel::panel.auth.name'),
                    'type' => 'text',
                    'autocomplete' => 'name',
                ])

                @include('panel::partials.profile-field', [
                    'name' => 'email',
                    'label' => __('panel::panel.auth.email'),
                    'type' => 'email',
                    'autocomplete' => 'username',
                ])
            </section>

            <section class="space-y-4 border-t border-[rgb(var(--panel-border))] pt-6">
                <div>
                    <h2 class="panel-profile-section-title">{{ __('panel::panel.profile.security_section') }}</h2>
                    <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.profile.security_hint') }}</p>
                </div>

                @include('panel::partials.profile-field', [
                    'name' => 'current_password',
                    'label' => __('panel::panel.profile.current_password'),
                    'type' => 'password',
                    'autocomplete' => 'current-password',
                ])

                @include('panel::partials.profile-field', [
                    'name' => 'password',
                    'label' => __('panel::panel.profile.new_password'),
                    'type' => 'password',
                    'autocomplete' => 'new-password',
                ])

                @include('panel::partials.profile-field', [
                    'name' => 'password_confirmation',
                    'label' => __('panel::panel.auth.password_confirm'),
                    'type' => 'password',
                    'autocomplete' => 'new-password',
                ])
            </section>
        </div>

        <div class="panel-form-footer">
            <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ __('panel::panel.save') }}</span>
                <span wire:loading wire:target="save">{{ __('panel::panel.loading') }}</span>
            </button>
        </div>
    </form>
</div>
