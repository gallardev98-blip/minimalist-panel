<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeScaffoldCommand extends Command
{
    protected $signature = 'panel:scaffold
                            {name : Nombre del modelo, ej. Product}
                            {--model= : Modelo Eloquent (por defecto = name)}
                            {--policy : Crear policy y enlazarla al resource}
                            {--widget= : Widget dashboard: stat|chart|resource-count}
                            {--force : Sobrescribir archivos existentes}';

    protected $description = 'Crea resource, policy opcional y widget opcional en un solo paso';

    public function handle(): int
    {
        $nombre = Str::studly($this->argument('name'));
        $modelo = Str::studly((string) ($this->option('model') ?: $nombre));
        $forzar = (bool) $this->option('force');
        $opciones = $forzar ? ['--force' => true] : [];

        $codigo = $this->call('panel:make-resource', array_merge([
            'name' => $nombre,
            '--model' => $modelo,
        ], $opciones));

        if ($codigo !== self::SUCCESS) {
            return $codigo;
        }

        if ($this->option('policy')) {
            $codigo = $this->call('panel:make-policy', array_merge([
                'name' => $modelo,
                '--model' => $modelo,
            ], $opciones));

            if ($codigo !== self::SUCCESS) {
                return $codigo;
            }
        }

        $tipoWidget = $this->option('widget');

        if (is_string($tipoWidget) && $tipoWidget !== '') {
            $argumentos = array_merge([
                'name' => $nombre,
                '--type' => $tipoWidget,
            ], $opciones);

            if ($tipoWidget === 'resource-count') {
                $argumentos['--resource'] = "{$nombre}Resource";
            }

            $codigo = $this->call('panel:make-widget', $argumentos);

            if ($codigo !== self::SUCCESS) {
                return $codigo;
            }

            $this->registrarWidgetEnConfig($nombre);
        }

        $this->components->info("Scaffold {$nombre} listo.");

        return self::SUCCESS;
    }

    private function registrarWidgetEnConfig(string $nombre): void
    {
        $clase = Str::endsWith($nombre, 'Widget') ? $nombre : "{$nombre}Widget";
        $fqn = "App\\Panel\\Widgets\\{$clase}";
        $configPath = config_path('panel.php');

        if (! File::exists($configPath)) {
            $this->line("Añade en config/panel.php → widgets => [{$fqn}::definir()];");

            return;
        }

        $config = File::get($configPath);

        if (str_contains($config, $fqn)) {
            return;
        }

        $linea = "        {$fqn}::definir(),";

        if (preg_match("/'widgets'\\s*=>\\s*\\[\\s*\\]/", $config)) {
            $actualizado = preg_replace(
                "/'widgets'\\s*=>\\s*\\[\\s*\\]/",
                "'widgets' => [\n{$linea}\n    ]",
                $config,
                1,
            );
        } else {
            $actualizado = preg_replace(
                "/('widgets'\\s*=>\\s*\\[)/",
                "$1\n{$linea}",
                $config,
                1,
            );
        }

        if (is_string($actualizado) && $actualizado !== $config) {
            File::put($configPath, $actualizado);
            $this->components->info('Widget registrado en config/panel.php');
        }
    }
}
