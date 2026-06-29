<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Filters;

use MyLaravelTools\Panel\Support\PanelConsultas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class MultiSelectFilter extends Filter
{
    /** @var array<string|int, string> */
    protected array $options = [];

    /** @var class-string<Model>|null */
    protected ?string $modeloRelacion = null;

    protected string $columnaTitulo = 'name';

    /** @param array<string|int, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;
        $this->modeloRelacion = null;

        return $this;
    }

    /** @param class-string<Model> $model */
    public function relationship(string $model, string $titleColumn = 'name'): static
    {
        $this->modeloRelacion = $model;
        $this->columnaTitulo = $titleColumn;
        $this->options = [];

        return $this;
    }

    public function getType(): string
    {
        return 'multi-select';
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value) || $value === []) {
            return $query;
        }

        return $query->whereIn($this->name, $value);
    }

    /** @return array<string|int, string> */
    private function resolverOpciones(): array
    {
        if ($this->options !== []) {
            return $this->options;
        }

        if ($this->modeloRelacion === null) {
            return [];
        }

        return PanelConsultas::opcionesRelacion($this->modeloRelacion, $this->columnaTitulo);
    }

    public function meta(): array
    {
        return [
            'options' => $this->resolverOpciones(),
        ];
    }
}
