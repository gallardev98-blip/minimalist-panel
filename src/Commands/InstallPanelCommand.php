<?php

declare(strict_types=1);

namespace Panel\Minimalist\Commands;

use Panel\Minimalist\Support\Package;
use Illuminate\Console\Command;

final class InstallPanelCommand extends Command
{
    protected $signature = 'panel:install {--force : Sobrescribir archivos publicados}';

    protected $description = 'Instala y publica la configuración del panel de administración';

    public function handle(): int
    {
        $this->components->info('Instalando '.Package::DISPLAY_NAME.'...');

        $this->call('vendor:publish', [
            '--tag' => 'panel-config',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->ensurePanelDirectoryExists();
        $this->printPostInstallInstructions();

        $this->components->info('Panel instalado correctamente.');

        return self::SUCCESS;
    }

    private function ensurePanelDirectoryExists(): void
    {
        $panelPath = app_path('Panel/Resources');

        if (! is_dir($panelPath)) {
            mkdir($panelPath, 0755, true);
            $this->components->info("Directorio creado: {$panelPath}");
        }
    }

    private function printPostInstallInstructions(): void
    {
        $vendorViews = './'.Package::vendorPath().'/resources/views/**/*.blade.php';
        $vendorViewsV4 = "@source '../../".Package::vendorPath()."/resources/views/**/*.blade.php';";

        $this->newLine();
        $this->line('  <fg=yellow>Siguientes pasos:</>');
        $this->newLine();
        $this->line('  1. Tailwind v3 — añade al content de tailwind.config.js:');
        $this->line("     '{$vendorViews}'");
        $this->newLine();
        $this->line('  2. Tailwind v4 — añade en resources/css/app.css:');
        $this->line("     {$vendorViewsV4}");
        $this->newLine();
        $this->line('  3. Auth integrada: /' . config('panel.path', 'admin') . '/login y /register');
        $this->line('  4. Crea resources: php artisan panel:make-resource Product --model=Product');
        $this->line('  5. Policies (opcional): php artisan panel:make-policy Product');
        $this->line('  6. Enlaza storage: php artisan storage:link');
        $this->line('  7. Visita: /' . config('panel.path', 'admin'));
        $this->newLine();
    }
}
