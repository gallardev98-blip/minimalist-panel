<?php

declare(strict_types=1);

namespace Panel\Minimalist\Widgets;

use Closure;

final class StatWidget
{
    private string $label;

    private Closure $valueResolver;

    private ?string $icon = null;

    private ?string $url = null;

    private string $color = 'indigo';

    private function __construct(string $label, Closure $valueResolver)
    {
        $this->label = $label;
        $this->valueResolver = $valueResolver;
    }

    public static function make(string $label, int|float|string|Closure $value): self
    {
        $resolver = is_callable($value)
            ? $value
            : fn (): int|float|string => $value;

        return new self($label, $resolver);
    }

    public function icon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string|int|float
    {
        return ($this->valueResolver)();
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
