<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Resources\Resource;
use MyLaravelTools\Panel\Support\ResourceRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakePolicyCommand extends Command
{
    protected $signature = 'panel:make-policy
                            {name : Nombre del modelo o resource, ej. Product}
                            {--model= : Modelo Eloquent asociado}
                            {--resource= : Resource class FQCN para enlazar $policy}
                            {--force : Sobrescribir si ya existe}';

    protected $description = 'Crea una Policy de Laravel para un Resource del panel';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $model = Str::studly($this->option('model') ?: $name);
        $policyClass = "{$model}Policy";
        $namespace = rtrim((string) config('panel.policies.namespace', 'App\\Policies'), '\\');
        $targetPath = app_path('Policies/' . $policyClass . '.php');
        $modelFqn = 'App\\Models\\' . $model;
        $userFqn = (string) config('auth.providers.users.model', 'App\\Models\\User');

        if (File::exists($targetPath) && ! $this->option('force')) {
            $this->components->error("La policy ya existe: {$targetPath}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Policies'));

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ policyClass }}',
                '{{ modelFqn }}',
                '{{ modelShort }}',
                '{{ userFqn }}',
                '{{ userShort }}',
            ],
            [
                $namespace,
                $policyClass,
                $modelFqn,
                $model,
                $userFqn,
                class_basename($userFqn),
            ],
            File::get(__DIR__ . '/../../stubs/policy.stub'),
        );

        File::put($targetPath, $content);

        $policyFqn = $namespace . '\\' . $policyClass;
        $this->linkPolicyOnResource($model, $policyFqn);

        $this->components->info("Policy creada: {$targetPath}");
        $this->line('Se registra automáticamente si panel.policies.auto_register es true.');

        return self::SUCCESS;
    }

    private function linkPolicyOnResource(string $model, string $policyFqn): void
    {
        $resourceFqn = $this->option('resource');

        if (is_string($resourceFqn) && $resourceFqn !== '') {
            $this->line("Enlaza en el Resource: protected static ?string \$policy = {$policyFqn}::class;");

            return;
        }

        foreach (app(ResourceRegistry::class)->all() as $resourceClass) {
            if (class_basename($resourceClass::modelClass()) !== $model) {
                continue;
            }

            $this->injectPolicyOnResource($resourceClass, $policyFqn);

            return;
        }

        $this->line("Añade en tu Resource: protected static ?string \$policy = {$policyFqn}::class;");
    }

    /** @param class-string<Resource> $resourceClass */
    private function injectPolicyOnResource(string $resourceClass, string $policyFqn): void
    {
        $reflection = new \ReflectionClass($resourceClass);
        $path = $reflection->getFileName();

        if ($path === false || ! File::exists($path)) {
            return;
        }

        $content = File::get($path);
        $shortPolicy = class_basename($policyFqn);
        $policyNamespace = Str::beforeLast($policyFqn, '\\');

        if (str_contains($content, '$policy')) {
            $this->components->info("{$resourceClass} ya declara \$policy.");

            return;
        }

        $modelNeedle = 'protected static string $model =';

        if (! str_contains($content, $modelNeedle)) {
            return;
        }

        $import = "use {$policyNamespace}\\{$shortPolicy};";
        if (! str_contains($content, $import)) {
            $content = preg_replace(
                '/(namespace [^;]+;\R\R)/',
                '$1' . $import . PHP_EOL,
                $content,
                1,
            ) ?? $content;
        }

        $updated = str_replace(
            $modelNeedle,
            "protected static ?string \$policy = {$shortPolicy}::class;\n\n    " . $modelNeedle,
            $content,
        );

        File::put($path, $updated);
        $this->components->info("Policy enlazada en {$resourceClass}");
    }
}
