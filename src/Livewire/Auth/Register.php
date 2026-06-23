<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Auth;

use MyLaravelTools\Panel\Support\PanelAuth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.guest')]
final class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $userModel = PanelAuth::userModel();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . $userModel . ',email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = $userModel::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        PanelAuth::assignRegisteredRole($user);

        if (PanelAuth::emailVerificationEnabled()) {
            PanelAuth::login($user);
            $this->redirect(\panel_route('verification.notice', [], false), navigate: false);

            return;
        }

        PanelAuth::login($user);

        $this->redirect(PanelAuth::redirectTargetAfterRegister(), navigate: false);
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.register')->title(__('panel::panel.auth.register_title'));
    }
}
