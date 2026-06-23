<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelRutas
{
    public static function nombre(string $ruta, ?string $panelId = null): string
    {
        return PanelManager::prefijoRuta($panelId).ltrim($ruta, '.');
    }

    /** @param array<string, mixed> $parametros */
    public static function url(string $ruta, array $parametros = [], bool $absoluta = true): string
    {
        return route(self::nombre($ruta), $parametros, $absoluta);
    }
}
