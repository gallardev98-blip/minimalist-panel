<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Controllers;

use MyLaravelTools\Panel\Support\PanelAuth;
use MyLaravelTools\Panel\Support\PanelImpersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (PanelImpersonation::isActive()) {
            PanelImpersonation::leave();
        }

        Auth::guard(PanelAuth::guard())->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new RedirectResponse(route(PanelAuth::loginRouteName()));
    }
}
