<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Middleware;

use MyLaravelTools\Panel\Support\PanelLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetPanelLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        PanelLocale::apply();

        return $next($request);
    }
}
