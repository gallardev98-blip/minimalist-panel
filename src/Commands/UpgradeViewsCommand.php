<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\Package;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

final class UpgradeViewsCommand extends Command
{
    protected $signature = 'panel:upgrade-views
                            {--force : Sobrescribir todas las vistas publicadas}
                            {--dry-run : Solo listar vistas desactualizadas}';

    protected $description = 'Compara y actualiza las vistas publicadas del panel respecto al paquete';

    public function handle(): int
    {
        $origen = realpath(__DIR__.'/../../resources/views');

        if ($origen === false) {
            $this->components->error('No se encontraron vistas del paquete.');

            return self::FAILURE;
        }

        $destino = resource_path('views/vendor/panel');

        if (! is_dir($destino)) {
            $this->components->warn('No hay vistas publicadas en resources/views/vendor/panel.');
            $this->components->info('Ejecuta: php artisan vendor:publish --tag=panel-views');

            return self::SUCCESS;
        }

        $desactualizadas = $this->vistasDesactualizadas($origen, $destino);

        if ($desactualizadas === []) {
            $this->components->info('Todas las vistas publicadas están al día.');

            return self::SUCCESS;
        }

        $this->components->warn(count($desactualizadas).' vista(s) desactualizada(s):');

        foreach ($desactualizadas as $ruta) {
            $this->line("  - {$ruta}");
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->components->info('Modo dry-run: no se ha modificado nada. Usa --force para republicar.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('¿Republicar todas las vistas del panel (--force)?', false)) {
            return self::SUCCESS;
        }

        $this->call('vendor:publish', [
            '--tag' => 'panel-views',
            '--force' => true,
        ]);

        $this->call('view:clear');
        $this->components->info('Vistas de '.Package::DISPLAY_NAME.' actualizadas.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function vistasDesactualizadas(string $origen, string $destino): array
    {
        $lista = [];

        foreach (Finder::create()->files()->in($origen)->name('*.blade.php') as $archivo) {
            $relativa = str_replace('\\', '/', $archivo->getRelativePathname());
            $publicada = $destino.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativa);

            if (! is_file($publicada)) {
                $lista[] = $relativa.' (no publicada)';

                continue;
            }

            if (md5_file($archivo->getRealPath()) !== md5_file($publicada)) {
                $lista[] = $relativa;
            }
        }

        sort($lista);

        return $lista;
    }
}
