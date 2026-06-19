<?php

declare(strict_types=1);

namespace Panel\Minimalist\Http\Middleware;

use Panel\Minimalist\Support\PanelAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RedirectIfPanelAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = PanelAuth::guard();

        if (auth($guard)->check()) {
            return redirect()->route(PanelAuth::redirectAfterLogin());
        }

        return $next($request);
    }
}
