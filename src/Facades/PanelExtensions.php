<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Facades;

use MyLaravelTools\Panel\Support\PanelExtensions as PanelExtensionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void registrarVistaCampo(string $tipo, string $vista)
 * @method static void registrarVistaColumna(string $tipo, string $vista)
 * @method static void registrarWidget(object $widget)
 * @method static string|null vistaCampo(string $tipo)
 * @method static string|null vistaColumna(string $tipo)
 * @method static void registrarSlot(string $nombre, string $vista)
 *
 * @see PanelExtensionsManager
 */
final class PanelExtensions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PanelExtensionsManager::class;
    }
}
