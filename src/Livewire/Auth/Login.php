<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Auth;

use MyLaravelTools\Panel\Livewire\Concerns\DispatchesPanelAuthAlert;
use MyLaravelTools\Panel\Support\PanelAuth;
use MyLaravelTools\Panel\Support\PanelValidation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.guest')]
final class Login extends Component
{
    use DispatchesPanelAuthAlert;
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate(
            PanelValidation::loginCredentials(),
            PanelValidation::loginMessages(),
        );

        $guard = PanelAuth::guard();

        if (! Auth::guard($guard)->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember,
        )) {
            $this->alertAuthFailure(__('panel::panel.auth.failed'));

            return;
        }

        $this->redirect(PanelAuth::redirectTargetAfterAuth(), navigate: false);
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.login', [
            'registrationEnabled' => PanelAuth::registrationEnabled(),
            'passwordResetEnabled' => PanelAuth::passwordResetEnabled(),
        ])->title(__('panel::panel.auth.login_title'));
    }
}
