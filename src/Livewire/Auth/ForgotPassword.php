<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Auth;

use MyLaravelTools\Panel\Support\PanelAuthMessages;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.guest')]
final class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => PanelAuthMessages::passwordStatus($status),
            ]);
        }

        $this->statusMessage = PanelAuthMessages::passwordStatus($status);
        $this->reset('email');
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.forgot-password')->title(__('panel::panel.auth.forgot_title'));
    }
}
