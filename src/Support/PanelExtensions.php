<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelExtensions
{
    /** @var array<string, string> */
    private array $vistasCampos = [];

    /** @var array<string, string> */
    private array $vistasColumnas = [];

    /** @var array<int, object> */
    private array $widgets = [];

    public function registrarVistaCampo(string $tipo, string $vista): void
    {
        $this->vistasCampos[$tipo] = $vista;
    }

    public function registrarVistaColumna(string $tipo, string $vista): void
    {
        $this->vistasColumnas[$tipo] = $vista;
    }

    public function registrarWidget(object $widget): void
    {
        $this->widgets[] = $widget;
    }

    public function registrarSlot(string $nombre, string $vista): void
    {
        app(PanelSlots::class)->registrar($nombre, $vista);
    }

    public function vistaCampo(string $tipo): ?string
    {
        return $this->vistasCampos[$tipo] ?? null;
    }

    public function vistaColumna(string $tipo): ?string
    {
        return $this->vistasColumnas[$tipo] ?? null;
    }

    /** @return array<int, object> */
    public function widgetsRegistrados(): array
    {
        return $this->widgets;
    }

    public function reiniciarDesdeConfig(): void
    {
        $this->vistasCampos = [];
        $this->vistasColumnas = [];
        $this->widgets = [];
        $this->aplicarDesdeConfig();
    }

    public function aplicarDesdeConfig(): void
    {
        $extensiones = config('panel.extensions', []);

        foreach (($extensiones['field_views'] ?? []) as $tipo => $vista) {
            if (is_string($tipo) && is_string($vista) && $vista !== '') {
                $this->registrarVistaCampo($tipo, $vista);
            }
        }

        foreach (($extensiones['column_views'] ?? []) as $tipo => $vista) {
            if (is_string($tipo) && is_string($vista) && $vista !== '') {
                $this->registrarVistaColumna($tipo, $vista);
            }
        }

        foreach (($extensiones['widgets'] ?? []) as $widget) {
            if (is_object($widget)) {
                $this->registrarWidget($widget);
            }
        }
    }
}
