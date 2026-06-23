<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelConfigUpgrader
{
    /** @return array<string, mixed> */
    public static function defecto(): array
    {
        return require dirname(__DIR__, 2).'/config/panel.php';
    }

    /** @param array<string, mixed> $host */
    public static function fusionar(array $host): array
    {
        return array_replace_recursive(self::defecto(), $host);
    }

    /**
     * @param array<string, mixed> $antes
     * @param array<string, mixed> $despues
     * @return list<string>
     */
    public static function clavesAnadidas(array $antes, array $despues, string $prefijo = ''): array
    {
        $lista = [];

        foreach ($despues as $clave => $valor) {
            if (! is_string($clave) && ! is_int($clave)) {
                continue;
            }

            $ruta = $prefijo === '' ? (string) $clave : "{$prefijo}.{$clave}";

            if (! array_key_exists($clave, $antes)) {
                $lista[] = $ruta;

                continue;
            }

            if (is_array($valor) && is_array($antes[$clave])) {
                $lista = array_merge($lista, self::clavesAnadidas($antes[$clave], $valor, $ruta));
            }
        }

        return $lista;
    }

    /** @param array<string, mixed> $config */
    public static function exportarPhp(array $config): string
    {
        $exportado = var_export($config, true);

        return "<?php\n\ndeclare(strict_types=1);\n\n/*\n| Actualizado por php artisan panel:upgrade-config\n| Revisa valores antes de desplegar.\n*/\n\nreturn {$exportado};\n";
    }
}
