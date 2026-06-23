<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Http\Middleware;

use MyLaravelTools\Panel\Support\PanelManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetCurrentPanel
{
    public function handle(Request $request, Closure $next, string $panelId): Response
    {
        PanelManager::establecerContexto($panelId);

        return $next($request);
    }
}
