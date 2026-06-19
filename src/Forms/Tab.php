<?php

declare(strict_types=1);

namespace Panel\Minimalist\Forms;

use Panel\Minimalist\Fields\Field;
use Illuminate\Support\Str;

final class Tab
{
    /**
     * @param array<int, Field|Section> $schema
     */
    private function __construct(
        private readonly string $label,
        private readonly string $id,
        private readonly array $schema,
    ) {}

    /**
     * @param array<int, Field|Section> $schema
     */
    public static function make(string $label, array $schema, ?string $id = null): self
    {
        return new self($label, $id ?? Str::slug($label), $schema);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /** @return array<int, Field|Section> */
    public function getSchema(): array
    {
        return $this->schema;
    }
}
