<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Commands;

use MyLaravelTools\Panel\Support\PanelDoctor;
use Illuminate\Console\Command;

final class DoctorPanelCommand extends Command
{
    protected $signature = 'panel:doctor';

    protected $description = 'Comprueba que el panel está bien configurado en la app host';

    public function handle(): int
    {
        $errores = 0;
        $avisos = 0;

        foreach (PanelDoctor::diagnosticar() as $fila) {
            $icono = match ($fila['nivel']) {
                'ok' => '<fg=green>✓</>',
                'warn' => '<fg=yellow>!</>',
                default => '<fg=red>✗</>',
            };

            $this->line(" {$icono} {$fila['mensaje']}");

            if ($fila['nivel'] === 'error') {
                $errores++;
            }

            if ($fila['nivel'] === 'warn') {
                $avisos++;
            }
        }

        $this->newLine();

        if ($errores > 0) {
            $this->components->error("{$errores} error(es) encontrado(s).");

            return self::FAILURE;
        }

        if ($avisos > 0) {
            $this->components->warn("Panel operativo con {$avisos} aviso(s).");

            return self::SUCCESS;
        }

        $this->components->info('Panel correctamente configurado.');

        return self::SUCCESS;
    }
}
