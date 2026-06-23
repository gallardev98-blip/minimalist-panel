<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\Package;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class InstallPanelCommand extends Command
{
    protected $signature = 'panel:install
                            {--force : Sobrescribir archivos publicados}
                            {--demo : Instalar ejemplo (navigation + resource)}
                            {--starter : Kit completo: demo + modelo + migración + widget}
                            {--saas : Kit SaaS: tenant + extensiones + vistas + widget}
                            {--multi : Multi-panel: panel-admin.php + panel-cliente.php + raíz}';

    protected $description = 'Instala y publica la configuración del panel de administración';

    public function handle(): int
    {
        $this->components->info('Instalando '.Package::DISPLAY_NAME.'...');

        $this->call('vendor:publish', [
            '--tag' => 'panel-config',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->ensurePanelDirectoryExists();

        if ($this->option('multi')) {
            $this->instalarMulti();
        } elseif ($this->option('saas')) {
            $this->instalarSaas();
        } elseif ($this->option('starter')) {
            $this->instalarStarter();
        } elseif ($this->option('demo')) {
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
        $this->line('  5. Crea resources: php artisan panel:scaffold Product --policy --widget=resource-count');
        $this->line('  6. Policies (opcional): php artisan panel:make-policy Product');
        $this->line('  7. Enlaza storage: php artisan storage:link');
        $this->line('  8. Visita: /' . config('panel.path', 'admin'));
        $this->line('  9. APP_URL en .env debe coincidir con host y puerto (p. ej. http://127.0.0.1:8000)');

        if ($this->option('starter')) {
            $this->newLine();
            $this->line('  <fg=green>Starter:</> php artisan migrate && visita /' . config('panel.path', 'admin'));
        }

        if ($this->option('multi')) {
            $this->newLine();
            $this->line('  <fg=green>Multi-panel:</> /admin y /cliente — rutas panel.admin.* y panel.cliente.*');
            $this->line('  Edita config/panel-admin.php y config/panel-cliente.php');
        }

        if ($this->option('saas')) {
            $this->newLine();
            $this->line('  <fg=green>SaaS:</> php artisan migrate && visita /'.config('panel.path', 'admin'));
            $this->line('  Extensiones en config/panel.php → extensions + slots');
        }

        $this->newLine();
    }

    private function instalarMulti(): void
    {
        $configPath = config_path('panel.php');
        $actual = is_file($configPath) ? require $configPath : [];

        if (is_array($actual) && isset($actual['panels']) && is_array($actual['panels']) && $actual['panels'] !== []) {
            $this->components->warn('config/panel.php ya define panels — no se modificó la raíz');

            return;
        }

        $adminPath = config_path('panel-admin.php');
        if (! is_file($adminPath)) {
            $admin = is_array($actual) && $actual !== []
                ? array_diff_key($actual, array_flip(['default', 'panels', 'documentation']))
                : require dirname(__DIR__, 2).'/stubs/multi/panel-admin.stub.php';
            $this->escribirConfigPhp($adminPath, $admin);
            $this->components->info('Creado config/panel-admin.php');
        }

        $this->copiarStub('multi/panel-cliente.stub.php', config_path('panel-cliente.php'), 'config/panel-cliente.php');

        File::copy(
            dirname(__DIR__, 2).'/stubs/multi/panel-root.stub.php',
            $configPath,
        );
        $this->components->info('config/panel.php actualizado (modo multi-panel)');
    }

    /** @param array<string, mixed> $datos */
    private function escribirConfigPhp(string $ruta, array $datos): void
    {
        $exportado = var_export($datos, true);
        File::put($ruta, "<?php\n\ndeclare(strict_types=1);\n\nreturn {$exportado};\n");
    }

    private function instalarStarter(): void
    {
        $this->instalarDemo();
        $this->copiarStub('starter/Post.stub', app_path('Models/Post.php'), 'Modelo Post');
        $this->copiarStub('starter/PostCountWidget.stub', app_path('Panel/Widgets/PostCountWidget.php'), 'Widget PostCountWidget');
        $this->copiarMigracionStarter();
        $this->parchearConfigStarter();
    }

    private function instalarSaas(): void
    {
        $this->copiarStub('saas/panel-navigation.stub', config_path('panel-navigation.php'), 'config/panel-navigation.php (SaaS)');
        $this->copiarStub('saas/Tenant.stub', app_path('Models/Tenant.php'), 'Modelo Tenant');
        $this->copiarStub('saas/TenantResource.stub', app_path('Panel/Resources/TenantResource.php'), 'TenantResource');
        $this->copiarStub('saas/TenantCountWidget.stub', app_path('Panel/Widgets/TenantCountWidget.php'), 'TenantCountWidget');
        $this->copiarMigracionSaas();
        $this->copiarVistasSaas();
        $this->parchearConfigSaas();
    }

    private function copiarMigracionSaas(): void
    {
        $origen = dirname(__DIR__, 2).'/stubs/saas/create_tenants_table.stub';

        if (! is_file($origen) || glob(database_path('migrations/*_create_tenants_table.php'))) {
            return;
        }

        $nombre = date('Y_m_d_His').'_create_tenants_table.php';
        copy($origen, database_path('migrations/'.$nombre));
        $this->components->info("Migración creada: database/migrations/{$nombre}");
    }

    private function copiarVistasSaas(): void
    {
        $mapa = [
            'campo-plan.stub' => 'campo-plan.blade.php',
            'columna-plan.stub' => 'columna-plan.blade.php',
            'aviso-tenant.stub' => 'aviso-tenant.blade.php',
        ];

        foreach ($mapa as $origen => $destino) {
            $ruta = resource_path('views/panel/saas/'.$destino);

            if (is_file($ruta)) {
                continue;
            }

            File::ensureDirectoryExists(dirname($ruta));
            copy(dirname(__DIR__, 2).'/stubs/saas/'.$origen, $ruta);
        }

        $this->components->info('Vistas SaaS en resources/views/panel/saas/');
    }

    private function parchearConfigSaas(): void
    {
        $configPath = config_path('panel.php');

        if (! is_file($configPath)) {
            return;
        }

        $config = File::get($configPath);
        $widgetFqn = 'App\\Panel\\Widgets\\TenantCountWidget';

        if (str_contains($config, "'navigation' => null")) {
            $config = str_replace(
                "'navigation' => null",
                "'navigation' => require __DIR__.'/panel-navigation.php'",
                $config,
            );
        }

        if (! str_contains($config, 'saas-plan')) {
            $config = preg_replace(
                "/'field_views'\\s*=>\\s*\\[\\s*\\]/",
                "'field_views' => [\n            'saas-plan' => 'panel.saas.campo-plan',\n        ]",
                $config,
                1,
            ) ?? $config;
            $config = preg_replace(
                "/'column_views'\\s*=>\\s*\\[\\s*\\]/",
                "'column_views' => [\n            'saas-plan' => 'panel.saas.columna-plan',\n        ]",
                $config,
                1,
            ) ?? $config;
        }

        if (! str_contains($config, 'panel.saas.aviso-tenant')) {
            $config = str_replace(
                "'main.before' => null",
                "'main.before' => 'panel.saas.aviso-tenant'",
                $config,
            );
        }

        if (! str_contains($config, $widgetFqn)) {
            if (preg_match("/'widgets'\\s*=>\\s*\\[\\s*\\]/", $config)) {
                $config = preg_replace(
                    "/'widgets'\\s*=>\\s*\\[\\s*\\]/",
                    "'widgets' => [\n        {$widgetFqn}::definir(),\n    ]",
                    $config,
                    1,
                ) ?? $config;
            } else {
                $config = preg_replace(
                    "/('widgets'\\s*=>\\s*\\[)/",
                    "$1\n        {$widgetFqn}::definir(),",
                    $config,
                    1,
                ) ?? $config;
            }
        }

        File::put($configPath, $config);
        $this->components->info('config/panel.php actualizado (extensions, slots, widgets, navigation)');
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

        if (! $this->option('starter')) {
            $this->line('  Demo: añade en config/panel.php → \'navigation\' => require __DIR__.\'/panel-navigation.php\'');
            $this->line('  Demo: crea modelo Post + migración si usas PostResource');
            $this->newLine();
        }
    }

    private function copiarStub(string $relativo, string $destino, string $etiqueta): void
    {
        $origen = dirname(__DIR__, 2).'/stubs/'.$relativo;

        if (is_file($destino) || ! is_file($origen)) {
            return;
        }

        if (! is_dir(dirname($destino))) {
            mkdir(dirname($destino), 0755, true);
        }

        copy($origen, $destino);
        $this->components->info("Creado {$etiqueta}");
    }

    private function copiarMigracionStarter(): void
    {
        $origen = dirname(__DIR__, 2).'/stubs/starter/create_posts_table.stub';

        if (! is_file($origen)) {
            return;
        }

        $nombre = date('Y_m_d_His').'_create_posts_table.php';
        $destino = database_path('migrations/'.$nombre);

        if (glob(database_path('migrations/*_create_posts_table.php'))) {
            return;
        }

        copy($origen, $destino);
        $this->components->info("Migración creada: database/migrations/{$nombre}");
    }

    private function parchearConfigStarter(): void
    {
        $configPath = config_path('panel.php');

        if (! is_file($configPath)) {
            return;
        }

        $config = File::get($configPath);
        $resourceFqn = 'App\\Panel\\Resources\\PostResource';
        $widgetFqn = 'App\\Panel\\Widgets\\PostCountWidget';

        if (! str_contains($config, $resourceFqn)) {
            $config = preg_replace(
                "/'resources'\\s*=>\\s*\\[([^\\]]*)\\]/",
                "'resources' => [\n        {$resourceFqn}::class,\$1]",
                $config,
                1,
            ) ?? $config;
        }

        if (str_contains($config, "'navigation' => null")) {
            $config = str_replace(
                "'navigation' => null",
                "'navigation' => require __DIR__.'/panel-navigation.php'",
                $config,
            );
        }

        if (! str_contains($config, $widgetFqn)) {
            if (preg_match("/'widgets'\\s*=>\\s*\\[\\s*\\]/", $config)) {
                $config = preg_replace(
                    "/'widgets'\\s*=>\\s*\\[\\s*\\]/",
                    "'widgets' => [\n        {$widgetFqn}::definir(),\n    ]",
                    $config,
                    1,
                ) ?? $config;
            } else {
                $config = preg_replace(
                    "/('widgets'\\s*=>\\s*\\[)/",
                    "$1\n        {$widgetFqn}::definir(),",
                    $config,
                    1,
                ) ?? $config;
            }
        }

        File::put($configPath, $config);
        $this->components->info('config/panel.php actualizado (navigation, resources, widgets)');
    }
}
