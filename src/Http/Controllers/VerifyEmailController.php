<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

final class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect(\panel_route('dashboard', [], false))
            ->with('status', __('panel::panel.auth.verification.verified'));
    }
}
