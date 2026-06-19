<?php

declare(strict_types=1);

namespace Panel\Minimalist\Widgets;

use Panel\Minimalist\Resources\Resource;

final class ResourceCountWidget
{
    private ?string $label = null;

    private string $color = 'indigo';

    /** @param class-string<Resource> $resourceClass */
    private function __construct(
        private readonly string $resourceClass,
    ) {}

    /** @param class-string<Resource> $resourceClass */
    public static function make(string $resourceClass): self
    {
        return new self($resourceClass);
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->resourceClass::label();
    }

    public function getValue(): int
    {
        if (! $this->resourceClass::authorize('viewAny')) {
            return 0;
        }

        return $this->resourceClass::modelClass()::query()->count();
    }

    public function getIcon(): ?string
    {
        return $this->resourceClass::icon();
    }

    public function getUrl(): ?string
    {
        if (! $this->resourceClass::authorize('viewAny')) {
            return null;
        }

        return route('panel.resources.index', ['resource' => $this->resourceClass::slug()]);
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
