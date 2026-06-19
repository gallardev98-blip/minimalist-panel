<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Illuminate\Database\Eloquent\Model;

final class NumberField extends Field
{
    protected ?float $min = null;

    protected ?float $max = null;

    protected ?float $step = null;

    public function min(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(float $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getType(): string
    {
        return 'number';
    }

    /** @return array<int, string> */
    public function getRules(?Model $record = null): array
    {
        $rules = array_merge(parent::getRules($record), ['numeric']);

        if ($this->min !== null) {
            $rules[] = 'min:' . $this->min;
        }

        if ($this->max !== null) {
            $rules[] = 'max:' . $this->max;
        }

        return $rules;
    }

    protected function meta(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step ?? 'any',
        ];
    }
}
