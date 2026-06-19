<?php

declare(strict_types=1);

namespace Panel\Minimalist\Columns;

use Illuminate\Database\Eloquent\Model;

abstract class Column
{
    protected string $name;

    protected ?string $label = null;

    protected bool $sortable = false;

    protected bool $searchable = false;

    final public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = str($name)->headline()->toString();
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    abstract public function getType(): string;

    public function resolve(Model $record): mixed
    {
        return data_get($record, $this->name);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'meta' => $this->meta(),
        ];
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [];
    }
}
