<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\Package;
use Illuminate\Console\Command;

final class InstallPanelCommand extends Command
{
    protected $signature = 'panel:install {--force : Sobrescribir archivos publicados} {--demo : Instalar ejemplo (navigation + resource)}';

    protected $description = 'Instala y publica la configuración del panel de administración';

    public function handle(): int
    {
        $this->components->info('Instalando '.Package::DISPLAY_NAME.'...');

        $this->call('vendor:publish', [
            '--tag' => 'panel-config',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->ensurePanelDirectoryExists();

        if ($this->option('demo')) {
            $this->instalarDemo();
        }

        $this->ensureLivewireConfigPublished();
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

    private function ensureLivewireConfigPublished(): void
    {
        $configPath = config_path('livewire.php');

        if (is_file($configPath)) {
            return;
        }

        $this->call('vendor:publish', [
            '--tag' => 'livewire:config',
        ]);
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
        $this->line('  4. Livewire: mantén navigate.show_progress_bar = true (NProgress se oculta vía CSS del panel; false rompe Alpine al cargar)');
        $this->line('  5. Crea resources: php artisan panel:make-resource Product --model=Product');
        $this->line('  6. Policies (opcional): php artisan panel:make-policy Product');
        $this->line('  7. Enlaza storage: php artisan storage:link');
        $this->line('  8. Visita: /' . config('panel.path', 'admin'));
        $this->line('  9. APP_URL en .env debe coincidir con host y puerto (p. ej. http://127.0.0.1:8000)');
        $this->newLine();
    }

    private function instalarDemo(): void
    {
        $navPath = config_path('panel-navigation.php');
        $stubNav = dirname(__DIR__, 2).'/stubs/demo/panel-navigation.stub';

        if (! is_file($navPath) && is_file($stubNav)) {
            copy($stubNav, $navPath);
            $this->components->info('Creado config/panel-navigation.php (demo)');
        }

        $resourcePath = app_path('Panel/Resources/PostResource.php');
        $stubResource = dirname(__DIR__, 2).'/stubs/demo/PostResource.stub';

        if (! is_file($resourcePath) && is_file($stubResource)) {
            if (! is_dir(dirname($resourcePath))) {
                mkdir(dirname($resourcePath), 0755, true);
            }

            copy($stubResource, $resourcePath);
            $this->components->info('Creado app/Panel/Resources/PostResource.php (demo)');
        }

        $this->line('  Demo: añade en config/panel.php → \'navigation\' => require __DIR__.\'/panel-navigation.php\'');
        $this->line('  Demo: crea modelo Post + migración si usas PostResource');
        $this->newLine();
    }
}
