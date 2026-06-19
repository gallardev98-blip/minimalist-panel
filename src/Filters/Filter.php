<?php

declare(strict_types=1);

namespace Panel\Minimalist\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected string $name;

    protected ?string $label = null;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    abstract public function getType(): string;

    abstract public function apply(Builder $query, mixed $value): Builder;

    /** @return array<string, mixed> */
    public function meta(): array
    {
        return [];
    }
}
