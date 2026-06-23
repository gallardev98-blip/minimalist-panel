<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Controllers;

use MyLaravelTools\Panel\Support\PanelImpersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ImpersonationController
{
    public function leave(Request $request): RedirectResponse
    {
        if (! PanelImpersonation::leave()) {
            abort(403);
        }

        return new RedirectResponse(\panel_route('dashboard', [], false));
    }
}
