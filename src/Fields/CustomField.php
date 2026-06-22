<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

final class CustomField extends Field
{
    protected string $tipoPersonalizado = 'custom';

    protected ?string $vista = null;

    public function type(string $tipo): static
    {
        $this->tipoPersonalizado = $tipo;

        return $this;
    }

    public function view(string $vista): static
    {
        $this->vista = $vista;

        return $this;
    }

    public function getType(): string
    {
        return $this->tipoPersonalizado;
    }

    public function getView(): ?string
    {
        return $this->vista;
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [
            'view' => $this->vista,
        ];
    }
}
