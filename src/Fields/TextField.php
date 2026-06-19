<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

final class TextField extends Field
{
    protected ?string $placeholder = null;

    protected int $maxLength = 255;

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function maxLength(int $maxLength): static
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function getType(): string
    {
        return 'text';
    }

    protected function meta(): array
    {
        return [
            'placeholder' => $this->placeholder,
            'maxLength' => $this->maxLength,
        ];
    }
}
