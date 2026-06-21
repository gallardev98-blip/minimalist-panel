<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Auth;

use MyLaravelTools\Panel\Support\PanelAuth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.guest')]
final class VerifyEmail extends Component
{
    public function sendVerification(): void
    {
        $user = PanelAuth::user();

        if ($user === null || ! method_exists($user, 'hasVerifiedEmail') || $user->hasVerifiedEmail()) {
            $this->redirect(route('panel.dashboard', [], false), navigate: false);

            return;
        }

        $user->sendEmailVerificationNotification();

        session()->flash('status', __('panel::panel.auth.verification.sent'));
    }

    public function logout(): void
    {
        Auth::guard(PanelAuth::guard())->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route(PanelAuth::loginRouteName(), [], false), navigate: false);
    }

    public function render(): mixed
    {
        return view('panel::livewire.auth.verify-email')->title(__('panel::panel.auth.verification.title'));
    }
}
