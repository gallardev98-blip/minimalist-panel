<?php

declare(strict_types=1);

use MyLaravelTools\Panel\Support\PanelRutas;

if (! function_exists('panel_route')) {
    /** @param array<string, mixed> $parametros */
    function panel_route(string $ruta, array $parametros = [], bool $absoluta = true): string
    {
        return PanelRutas::url($ruta, $parametros, $absoluta);
    }
}
