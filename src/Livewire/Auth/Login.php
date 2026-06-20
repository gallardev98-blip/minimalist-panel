<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Auth;

use MyLaravelTools\Panel\Support\PanelAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.guest')]
final class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $guard = PanelAuth::guard();

        if (! Auth::guard($guard)->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember,
        )) {
            throw ValidationException::withMessages([
                'email' => __('panel::panel.auth.failed'),
            ]);
        }

        $this->redirect(PanelAuth::redirectTargetAfterAuth(), navigate: true);
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.login', [
            'registrationEnabled' => PanelAuth::registrationEnabled(),
            'passwordResetEnabled' => PanelAuth::passwordResetEnabled(),
        ])->title(__('panel::panel.auth.login_title'));
    }
}
