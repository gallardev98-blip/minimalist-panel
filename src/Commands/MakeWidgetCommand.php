<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeWidgetCommand extends Command
{
    protected $signature = 'panel:make-widget
                            {name : Nombre del widget, ej. VentasMensuales}
                            {--type=chart : chart|stat|resource-count|view}
                            {--chart=bar : Tipo ChartWidget: bar|line|pie|doughnut|progression}
                            {--resource= : Clase Resource para resource-count, ej. ProductResource}
                            {--view= : Vista Blade para type=view}
                            {--force : Sobrescribir si ya existe}';

    protected $description = 'Crea una clase widget para el dashboard (config panel.widgets)';

    public function handle(): int
    {
        $nombre = Str::studly($this->argument('name'));
        $clase = Str::endsWith($nombre, 'Widget') ? $nombre : "{$nombre}Widget";
        $tipo = (string) $this->option('type');
        $ruta = app_path("Panel/Widgets/{$clase}.php");

        if (File::exists($ruta) && ! $this->option('force')) {
            $this->components->error("El widget ya existe: {$ruta}");

            return self::FAILURE;
        }

        $stub = $this->resolverStub($tipo);
        if ($stub === null) {
            $this->components->error("Tipo no válido: {$tipo}. Usa chart, stat, resource-count o view.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Panel/Widgets'));

        $etiqueta = Str::headline(str_replace('Widget', '', $clase));
        $reemplazos = [
            '{{ class }}' => $clase,
            '{{ label }}' => $etiqueta,
            '{{ chartType }}' => (string) $this->option('chart'),
            '{{ resourceClass }}' => Str::studly((string) $this->option('resource')),
            '{{ view }}' => (string) ($this->option('view') ?: 'panel.widgets.mi-widget'),
        ];

        File::put($ruta, str_replace(array_keys($reemplazos), array_values($reemplazos), $stub));

        $this->components->info("Widget creado: {$ruta}");
        $this->line("Añade en config/panel.php → 'widgets' => [");
        $this->line("    \\App\\Panel\\Widgets\\{$clase}::definir(),");
        $this->line('];');

        return self::SUCCESS;
    }

    private function resolverStub(string $tipo): ?string
    {
        $mapa = [
            'chart' => 'widget-chart.stub',
            'stat' => 'widget-stat.stub',
            'resource-count' => 'widget-resource-count.stub',
            'view' => 'widget-view.stub',
        ];

        $archivo = $mapa[$tipo] ?? null;
        if ($archivo === null) {
            return null;
        }

        $ruta = __DIR__."/../../stubs/{$archivo}";
        if (! is_file($ruta)) {
            return null;
        }

        return File::get($ruta);
    }
}
