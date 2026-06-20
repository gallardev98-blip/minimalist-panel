<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeResourceCommand extends Command
{
    protected $signature = 'panel:make-resource
                            {name : Nombre del resource, ej. User}
                            {--model= : Modelo Eloquent asociado}
                            {--force : Sobrescribir si ya existe}';

    protected $description = 'Crea un nuevo Resource declarativo para el panel';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $model = $this->option('model') ? Str::studly($this->option('model')) : $name;
        $resourceClass = "{$name}Resource";
        $targetPath = app_path("Panel/Resources/{$resourceClass}.php");

        if (File::exists($targetPath) && ! $this->option('force')) {
            $this->components->error("El resource ya existe: {$targetPath}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Panel/Resources'));

        $stub = File::get(__DIR__ . '/../../stubs/resource.stub');
        $content = str_replace(
            ['{{ resourceClass }}', '{{ modelClass }}', '{{ modelVariable }}'],
            [$resourceClass, $model, Str::camel($model)],
            $stub,
        );

        File::put($targetPath, $content);

        $this->registerResourceInConfig($resourceClass);

        $this->components->info("Resource creado: {$targetPath}");
        $this->line("Regístralo en config/panel.php si el instalador no lo hizo automáticamente.");

        return self::SUCCESS;
    }

    private function registerResourceInConfig(string $resourceClass): void
    {
        $configPath = config_path('panel.php');

        if (! File::exists($configPath)) {
            return;
        }

        $fqn = 'App\\Panel\\Resources\\' . $resourceClass;
        $config = File::get($configPath);

        if (str_contains($config, $fqn)) {
            return;
        }

        $updated = preg_replace(
            "/'resources'\\s*=>\\s*\\[([^\\]]*)\\]/",
            "'resources' => [\n        {$fqn}::class,\$1]",
            $config,
            1,
        );

        if (is_string($updated) && $updated !== $config) {
            File::put($configPath, $updated);
            $this->components->info("Resource registrado en config/panel.php");
        }
    }
}
