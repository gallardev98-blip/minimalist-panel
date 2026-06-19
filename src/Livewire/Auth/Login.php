<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire\Auth;

use Panel\Minimalist\Support\PanelAuth;
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

        session()->regenerate();

        $this->redirectIntended(route(PanelAuth::redirectAfterLogin()), navigate: false);
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.login', [
            'registrationEnabled' => PanelAuth::registrationEnabled(),
        ])->title(__('panel::panel.auth.login_title'));
    }
}
