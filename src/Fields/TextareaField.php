<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

final class TextareaField extends Field
{
    protected int $rows = 4;

    protected ?string $placeholder = null;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getType(): string
    {
        return 'textarea';
    }

    protected function meta(): array
    {
        return [
            'rows' => $this->rows,
            'placeholder' => $this->placeholder,
        ];
    }
}
