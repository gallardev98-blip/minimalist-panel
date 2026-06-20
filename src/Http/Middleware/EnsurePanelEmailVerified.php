<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MyLaravelTools\Panel\Support\PanelAuth;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePanelEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! PanelAuth::enabled() || ! PanelAuth::emailVerificationEnabled()) {
            return $next($request);
        }

        $user = PanelAuth::user();

        if ($user === null) {
            return $next($request);
        }

        if (! method_exists($user, 'hasVerifiedEmail') || $user->hasVerifiedEmail()) {
            return $next($request);
        }

        if ($request->routeIs('panel.verification.*')) {
            return $next($request);
        }

        return redirect()->route('panel.verification.notice', [], false);
    }
}
