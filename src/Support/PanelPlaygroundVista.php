<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelPlaygroundVista
{
    /** @return list<string> */
    public static function clavesModificadas(): array
    {
        $claves = array_keys(PanelPlayground::sobreescrituras());

        return array_values(array_filter($claves, fn (mixed $clave): bool => is_string($clave) && $clave !== ''));
    }

    /** @return list<string> */
    public static function zonasModificadas(): array
    {
        $zonas = [];

        foreach (self::clavesModificadas() as $clave) {
            $zonas[] = self::zonaPorClave($clave);
        }

        if (PanelPlaygroundGraficos::tieneCambios()) {
            $zonas[] = 'graficos';
        }

        return array_values(array_unique($zonas));
    }

    public static function zonaPorClave(string $clave): string
    {
        return match (true) {
            str_starts_with($clave, 'brand.') => 'marca',
            in_array($clave, ['layout.mode', 'layout.sidebar_position', 'layout.sidebar_collapsible', 'layout.sidebar_collapsed_width'], true) => 'menu',
            $clave === 'theme.sidebar_width' => 'menu',
            in_array($clave, ['layout.table_striped', 'layout.table_compact'], true) => 'tabla',
            in_array($clave, ['layout.content_width', 'layout.density', 'layout.show_breadcrumbs'], true) => 'contenido',
            str_starts_with($clave, 'theme.colors.') => 'acentos',
            str_starts_with($clave, 'theme.') => 'tema',
            str_starts_with($clave, 'auth_ui.') => 'auth',
            str_starts_with($clave, 'import.') => 'import',
            str_starts_with($clave, 'permissions.') => 'permisos',
            str_starts_with($clave, 'extensions.') => 'extensiones',
            str_starts_with($clave, 'layout.') => 'panel',
            default => 'panel',
        };
    }

    public static function etiquetaZona(string $zona): string
    {
        return (string) __("panel::panel.documentation.zones.{$zona}");
    }

    public static function pistaZona(string $clave): string
    {
        return __('panel::panel.documentation.zone_hint', [
            'zone' => self::etiquetaZona(self::zonaPorClave($clave)),
        ]);
    }

    public static function zonaEstaModificada(string $zona): bool
    {
        return in_array($zona, self::zonasModificadas(), true);
    }

    public static function claveEstaModificada(string $clave): bool
    {
        return array_key_exists($clave, PanelPlayground::sobreescrituras());
    }

    public static function contarCambiosSeccion(string $seccionId): int
    {
        $seccion = collect(PanelDocumentacion::secciones())->firstWhere('id', $seccionId);
        if (! is_array($seccion)) {
            return 0;
        }

        $modificadas = self::clavesModificadas();
        $total = 0;

        foreach ($seccion['opciones'] as $opcion) {
            if (in_array($opcion['clave'] ?? '', $modificadas, true)) {
                $total++;
            }
        }

        return $total;
    }
}
