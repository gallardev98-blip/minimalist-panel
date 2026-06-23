<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\Package;
use MyLaravelTools\Panel\Support\PanelConfigUpgrader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class UpgradeConfigCommand extends Command
{
    protected $signature = 'panel:upgrade-config
                            {--dry-run : Solo listar claves que se añadirían}
                            {--force : Escribir sin confirmación}';

    protected $description = 'Fusiona config/panel.php con las claves nuevas del paquete';

    public function handle(): int
    {
        $ruta = config_path('panel.php');

        if (! is_file($ruta)) {
            $this->components->error('No existe config/panel.php — ejecuta php artisan panel:install');

            return self::FAILURE;
        }

        $actual = require $ruta;

        if (! is_array($actual)) {
            $this->components->error('config/panel.php no devuelve un array');

            return self::FAILURE;
        }

        $fusionado = PanelConfigUpgrader::fusionar($actual);
        $anadidas = PanelConfigUpgrader::clavesAnadidas($actual, $fusionado);

        if ($anadidas === []) {
            $this->components->info('config/panel.php ya incluye todas las claves de '.Package::VERSION);

            return self::SUCCESS;
        }

        $this->components->warn(count($anadidas).' clave(s) nueva(s) respecto al paquete:');

        foreach ($anadidas as $clave) {
            $this->line("  + {$clave}");
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->components->info('Modo dry-run: no se modificó el archivo.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('¿Fusionar y guardar config/panel.php? (se crea backup)', true)) {
            return self::SUCCESS;
        }

        $backup = $ruta.'.bak.'.date('Y-m-d-His');
        File::copy($ruta, $backup);
        File::put($ruta, PanelConfigUpgrader::exportarPhp($fusionado));

        $this->components->info("Backup: {$backup}");
        $this->components->info('config/panel.php actualizado — revisa el diff antes de commit');

        return self::SUCCESS;
    }
}
