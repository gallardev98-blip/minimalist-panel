<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelIntegrations
{
    public static function alertasEnabled(): bool
    {
        if (! config('panel.integrations.alertas', true)) {
            return false;
        }

        return view()->exists('alertas::components.contenedor');
    }
}
