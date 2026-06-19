<?php

declare(strict_types=1);

namespace Panel\Minimalist\Forms;

use Panel\Minimalist\Fields\Field;

final class Section
{
    private ?string $description = null;

    /** @param array<int, Field> $fields */
    private function __construct(
        private readonly string $title,
        private readonly array $fields,
    ) {}

    /** @param array<int, Field> $fields */
    public static function make(string $title, array $fields): self
    {
        return new self($title, $fields);
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @return array<int, Field> */
    public function getFields(): array
    {
        return $this->fields;
    }
}
