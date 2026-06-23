<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelSlots
{
    /** @var array<string, string> */
    private array $vistas = [];

    public function registrar(string $nombre, string $vista): void
    {
        $this->vistas[$nombre] = $vista;
    }

    public function vista(string $nombre): ?string
    {
        return $this->vistas[$nombre] ?? null;
    }

    public function reiniciarDesdeConfig(): void
    {
        $this->vistas = [];
        $this->aplicarDesdeConfig();
    }

    public function aplicarDesdeConfig(): void
    {
        $slots = array_merge(
            config('panel.slots', []),
            config('panel.extensions.slots', []),
        );

        foreach ($slots as $nombre => $vista) {
            if (is_string($nombre) && is_string($vista) && $vista !== '') {
                $this->registrar($nombre, $vista);
            }
        }
    }
}
