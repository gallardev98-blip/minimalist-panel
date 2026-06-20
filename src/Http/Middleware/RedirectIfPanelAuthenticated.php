<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Middleware;

use MyLaravelTools\Panel\Support\PanelAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RedirectIfPanelAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = PanelAuth::guard();

        if (auth($guard)->check()) {
            return redirect()->to(route(PanelAuth::redirectAfterLogin(), [], false));
        }

        return $next($request);
    }
}
