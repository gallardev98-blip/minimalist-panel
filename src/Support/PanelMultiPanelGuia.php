<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelMultiPanelGuia
{
    public static function codigo(): string
    {
        return <<<'PHP'
// config/panel.php — varios paneles en la misma app
'panels' => [
    'admin' => [
        'path' => 'admin',
        'middleware' => ['web', /* middleware panel */],
    ],
    'cliente' => [
        'path' => 'cliente',
    ],
],
'default' => 'admin',

// Rutas: 1 panel → panel.* | 2+ paneles → panel.{id}.*
\panel_route('dashboard');                        // panel por defecto
\panel_route('dashboard', panel: 'cliente');      // panel.cliente.dashboard

php artisan panel:install --multi
php artisan panel:doctor
PHP;
    }

    /** @return list<array{id: string, path: string, rutas: string}> */
    public static function panelesDemo(): array
    {
        return [
            ['id' => 'admin', 'path' => '/admin', 'rutas' => 'panel.admin.*'],
            ['id' => 'cliente', 'path' => '/cliente', 'rutas' => 'panel.cliente.*'],
        ];
    }
}
