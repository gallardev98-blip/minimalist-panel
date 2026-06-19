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
        $this->ensureLivewireNavigateProgressDisabled();
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

    private function ensureLivewireNavigateProgressDisabled(): void
    {
        $configPath = config_path('livewire.php');

        if (! is_file($configPath)) {
            $this->call('vendor:publish', [
                '--tag' => 'livewire:config',
            ]);
        }

        if (! is_file($configPath)) {
            return;
        }

        $contents = (string) file_get_contents($configPath);

        if (! str_contains($contents, "'show_progress_bar' => true")) {
            return;
        }

        $updated = str_replace(
            "'show_progress_bar' => true",
            "'show_progress_bar' => false",
            $contents,
        );

        file_put_contents($configPath, $updated);
        $this->components->info('Livewire: show_progress_bar desactivado (usa el loader del panel).');
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
        $this->line('  4. Livewire: config/livewire.php con navigate.show_progress_bar = false (evita barra duplicada)');
        $this->line('  5. Crea resources: php artisan panel:make-resource Product --model=Product');
        $this->line('  6. Policies (opcional): php artisan panel:make-policy Product');
        $this->line('  7. Enlaza storage: php artisan storage:link');
        $this->line('  8. Visita: /' . config('panel.path', 'admin'));
        $this->newLine();
    }
}
