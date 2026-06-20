<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Widgets;

use Closure;
use MyLaravelTools\Panel\Support\ThemeResolver;

final class ChartWidget
{
    private string $label;

    private string $chartType;

    /** @var Closure(): array{labels: list<string>, values: list<int|float>} */
    private Closure $dataResolver;

    private ?string $url = null;

    private string $color = 'indigo';

    /** @var list<string> */
    private array $colors = [];

    private bool $useThemeColors = false;

    /** @var list<string>|null */
    private ?array $themeColorKeys = null;

    /** @var array<string, mixed> */
    private array $chartOptions = [];

    private int $height = 140;

    /** @param Closure(): array{labels: list<string>, values: list<int|float>} $dataResolver */
    private function __construct(string $label, string $chartType, Closure $dataResolver)
    {
        $this->label = $label;
        $this->chartType = $chartType;
        $this->dataResolver = $dataResolver;
    }

    /** @param Closure(): array{labels: list<string>, values: list<int|float>}|array{labels: list<string>, values: list<int|float>} $data */
    public static function make(string $label, string $type, Closure|array $data): self
    {
        $resolver = is_array($data) ? fn (): array => $data : $data;

        return new self($label, $type, $resolver);
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /** @param list<string> $colors */
    public function colors(array $colors): self
    {
        $this->colors = $colors;
        $this->useThemeColors = false;

        return $this;
    }

    /** Colores desde `config('panel.theme.colors')`. Sin claves: auto según tipo y datos. */
    public function themeColors(?array $keys = null): self
    {
        $this->useThemeColors = true;
        $this->themeColorKeys = $keys;

        return $this;
    }

    /** @param array<string, mixed> $options */
    public function options(array $options): self
    {
        $this->chartOptions = $options;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = max(80, $height);

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getChartType(): string
    {
        return $this->chartType;
    }

    public function isProgression(): bool
    {
        return $this->chartType === 'progression';
    }

    /** Tipo real para Chart.js (`progression` → `line`). */
    public function resolveChartType(): string
    {
        return $this->chartType === 'progression' ? 'line' : $this->chartType;
    }

    /** @return array{labels: list<string>, values: list<int|float>} */
    public function getChartData(): array
    {
        return ($this->dataResolver)();
    }

    /** @return list<string> */
    public function getColors(): array
    {
        return $this->colors;
    }

    /** Claves semánticas; el cliente las resuelve con `--panel-*` (respeta claro/oscuro). */
    /** @return list<string>|null */
    public function getThemeColorKeys(): ?array
    {
        if (! $this->useThemeColors) {
            return null;
        }

        return $this->themeColorKeys ?? ThemeResolver::defaultChartColorKeys(
            $this->chartType,
            count($this->getChartData()['values'] ?? [])
        );
    }

    /** @return array<string, mixed> */
    public function getChartOptions(): array
    {
        return $this->chartOptions;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getIcon(): ?string
    {
        return match ($this->chartType) {
            'pie', 'doughnut' => 'pie-chart',
            'line', 'progression' => 'trending-up',
            default => 'bar-chart',
        };
    }

    public function getValue(): string
    {
        return (string) array_sum($this->getChartData()['values'] ?? []);
    }
}
