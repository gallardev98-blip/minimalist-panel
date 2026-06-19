<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

final class SelectField extends Field
{
    /** @var array<string|int, string> */
    protected array $options = [];

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return 'select';
    }

    protected function meta(): array
    {
        return [
            'options' => $this->options,
        ];
    }
}
