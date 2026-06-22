<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\Finder;

final class PanelDoctor
{
    /** @return list<array{nivel: string, mensaje: string}> */
    public static function diagnosticar(): array
    {
        $resultados = [];

        $resultados[] = self::comprobarConfig();
        $resultados[] = self::comprobarRutaPlayground();
        $resultados[] = self::comprobarLivewireNavigate();
        $resultados[] = self::comprobarTailwind();
        $resultados[] = self::comprobarPermisos();
        $resultados[] = self::comprobarVistasPublicadas();
        $resultados[] = self::comprobarAlertas();

        return $resultados;
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarConfig(): array
    {
        if (! is_file(config_path('panel.php'))) {
            return ['nivel' => 'error', 'mensaje' => 'Falta config/panel.php — ejecuta php artisan panel:install'];
        }

        return ['nivel' => 'ok', 'mensaje' => 'config/panel.php presente'];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarRutaPlayground(): array
    {
        if (! config('panel.documentation.enabled', true)) {
            return ['nivel' => 'ok', 'mensaje' => 'Playground desactivado (documentation.enabled = false)'];
        }

        if (! Route::has('panel.playground')) {
            return ['nivel' => 'error', 'mensaje' => 'Ruta panel.playground no registrada'];
        }

        $path = trim((string) config('panel.documentation.path', 'playground'), '/');

        return ['nivel' => 'ok', 'mensaje' => "Playground activo en /{$path}"];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarLivewireNavigate(): array
    {
        $navigate = config('livewire.navigate.show_progress_bar');

        if ($navigate === false) {
            return [
                'nivel' => 'warn',
                'mensaje' => 'livewire.navigate.show_progress_bar = false puede romper Alpine en el panel',
            ];
        }

        return ['nivel' => 'ok', 'mensaje' => 'Livewire navigate configurado'];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarTailwind(): array
    {
        $candidatos = [
            base_path('tailwind.config.js'),
            base_path('tailwind.config.cjs'),
            base_path('vite.config.js'),
            resource_path('css/app.css'),
        ];

        $encontrado = false;
        $patrones = ['vendor/panel', 'mylaraveltools/panel', '@source', 'panel.css'];

        foreach ($candidatos as $ruta) {
            if (! is_file($ruta)) {
                continue;
            }

            $contenido = (string) File::get($ruta);
            foreach ($patrones as $patron) {
                if (str_contains($contenido, $patron)) {
                    $encontrado = true;

                    break 2;
                }
            }
        }

        if (! $encontrado) {
            return [
                'nivel' => 'warn',
                'mensaje' => 'No se detectaron rutas Tailwind del panel — revisa content/@source en tailwind/vite',
            ];
        }

        return ['nivel' => 'ok', 'mensaje' => 'Tailwind incluye referencias al panel'];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarPermisos(): array
    {
        if (! config('panel.permissions.enabled', false)) {
            return ['nivel' => 'ok', 'mensaje' => 'Permisos Spatie desactivados'];
        }

        if (! class_exists(\Spatie\Permission\PermissionServiceProvider::class)) {
            return [
                'nivel' => 'error',
                'mensaje' => 'permissions.enabled pero spatie/laravel-permission no está instalado',
            ];
        }

        return ['nivel' => 'ok', 'mensaje' => 'Spatie Permission disponible'];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarVistasPublicadas(): array
    {
        $destino = resource_path('views/vendor/panel');

        if (! is_dir($destino)) {
            return ['nivel' => 'ok', 'mensaje' => 'Vistas del vendor (no publicadas)'];
        }

        $origen = realpath(__DIR__.'/../../resources/views');
        if ($origen === false) {
            return ['nivel' => 'warn', 'mensaje' => 'No se pudo comparar vistas publicadas'];
        }

        $desactualizadas = 0;
        $finder = (new Finder)->files()->in($origen)->name('*.blade.php');

        foreach ($finder as $archivo) {
            $relativa = str_replace('\\', '/', $archivo->getRelativePathname());
            $publicada = $destino.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativa);

            if (! is_file($publicada)) {
                continue;
            }

            if (md5_file($archivo->getPathname()) !== md5_file($publicada)) {
                $desactualizadas++;
            }
        }

        if ($desactualizadas > 0) {
            return [
                'nivel' => 'warn',
                'mensaje' => "{$desactualizadas} vista(s) publicada(s) desactualizada(s) — panel:upgrade-views --force",
            ];
        }

        return ['nivel' => 'ok', 'mensaje' => 'Vistas publicadas al día'];
    }

    /** @return array{nivel: string, mensaje: string} */
    private static function comprobarAlertas(): array
    {
        if (! config('panel.integrations.alertas', true)) {
            return ['nivel' => 'ok', 'mensaje' => 'Integración Alertas desactivada en config'];
        }

        if (! class_exists(\MyLaravelTools\Alertas\AlertasServiceProvider::class)) {
            return ['nivel' => 'ok', 'mensaje' => 'Alertas no instalado (opcional)'];
        }

        return ['nivel' => 'ok', 'mensaje' => 'mylaraveltools/alertas detectado'];
    }
}
