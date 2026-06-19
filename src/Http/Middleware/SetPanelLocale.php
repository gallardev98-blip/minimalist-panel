<?php

declare(strict_types=1);

namespace Panel\Minimalist\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetPanelLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('panel.locale');

        if (is_string($locale) && $locale !== '') {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
