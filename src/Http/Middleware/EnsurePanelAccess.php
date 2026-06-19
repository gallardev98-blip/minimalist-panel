<?php

declare(strict_types=1);

namespace Panel\Minimalist\Http\Middleware;

use Panel\Minimalist\Support\PanelAuth;
use Panel\Minimalist\Support\PanelPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = PanelAuth::guard();

        if (! auth($guard)->check()) {
            return redirect()->guest(route(PanelAuth::loginRouteName()));
        }

        abort_unless(PanelPermission::panelAccessGranted(), 403);

        return $next($request);
    }
}
