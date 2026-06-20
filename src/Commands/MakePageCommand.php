<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakePageCommand extends Command
{
    protected $signature = 'panel:make-page
                            {name : Nombre de la página, ej. Settings}
                            {--view= : Vista Blade (default: panel.pages.{slug})}
                            {--force : Sobrescribir si ya existe}';

    protected $description = 'Crea una página custom del panel (informes, ajustes, etc.)';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $pageClass = Str::endsWith($name, 'Page') ? $name : "{$name}Page";
        $slug = Str::kebab(str_replace('Page', '', $pageClass));
        $targetPath = app_path("Panel/Pages/{$pageClass}.php");
        $viewSlug = Str::snake(str_replace('Page', '', $pageClass), '-');
        $view = $this->option('view') ?: "panel.pages.{$viewSlug}";

        if (File::exists($targetPath) && ! $this->option('force')) {
            $this->components->error("La página ya existe: {$targetPath}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Panel/Pages'));

        $stub = File::get(__DIR__ . '/../../stubs/page.stub');
        $content = str_replace(
            ['{{ pageClass }}', '{{ slug }}', '{{ label }}', '{{ view }}'],
            [$pageClass, $slug, Str::headline(str_replace('Page', '', $pageClass)), $view],
            $stub,
        );

        File::put($targetPath, $content);

        $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');

        if (! File::exists($viewPath)) {
            File::ensureDirectoryExists(dirname($viewPath));
            File::put($viewPath, $this->defaultViewStub($pageClass));
            $this->components->info("Vista creada: {$viewPath}");
        }

        $this->components->info("Página creada: {$targetPath}");
        $this->line('Añádela a config/panel-navigation.php con [\'page\' => ' . $pageClass . '::class]');

        return self::SUCCESS;
    }

    private function defaultViewStub(string $pageClass): string
    {
        return <<<BLADE
<div>
    <x-panel::page-header class="mb-8">
        <h1>{{ \$pageClass::label() }}</h1>
        <p class="panel-muted mt-1 text-sm">Página custom del panel.</p>
    </x-panel::page-header>

    <div class="panel-card p-6">
        <p class="panel-muted text-sm">Edita esta vista en resources/views.</p>
    </div>
</div>
BLADE;
    }
}
