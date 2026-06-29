<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\PanelConsultas;
use Illuminate\Console\Command;

final class AuditRendimientoCommand extends Command
{
    protected $signature = 'panel:audit-rendimiento';

    protected $description = 'Audita N+1, índices sugeridos y caché de opciones en los resources del panel';

    public function handle(): int
    {
        $avisos = 0;
        $infos = 0;

        foreach (PanelConsultas::auditarRecursosRegistrados() as $fila) {
            $icono = match ($fila['nivel']) {
                'ok' => '<fg=green>✓</>',
                'info' => '<fg=cyan>→</>',
                default => '<fg=yellow>!</>',
            };

            $this->line(" {$icono} {$fila['mensaje']}");

            if ($fila['nivel'] === 'warn') {
                $avisos++;
            }

            if ($fila['nivel'] === 'info') {
                $infos++;
            }
        }

        $this->newLine();
        $this->line('  Caché opciones filtros: '.(config('panel.performance.filter_options_cache', true) ? 'activa' : 'desactivada'));
        $this->line('  Eager load columnas: '.(config('panel.performance.eager_load_columns', true) ? 'activo' : 'desactivado'));
        $this->line('  Paginación cursor: '.(config('panel.performance.cursor_pagination', false) ? 'activa' : 'desactivada'));
        $this->newLine();

        if ($avisos > 0) {
            $this->components->warn("{$avisos} aviso(s), {$infos} sugerencia(s) de índice.");

            return self::SUCCESS;
        }

        $this->components->info($infos > 0
            ? "Sin avisos N+1. {$infos} sugerencia(s) de índice."
            : 'Sin avisos de rendimiento en los resources.');

        return self::SUCCESS;
    }
}
