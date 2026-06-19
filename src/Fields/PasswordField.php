<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

final class PasswordField extends Field
{
    protected bool $confirmed = false;

    public function confirmed(bool $confirmed = true): static
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getType(): string
    {
        return 'password';
    }

    public function augmentFormState(array $state): array
    {
        if ($this->confirmed) {
            $state[$this->name . '_confirmation'] = '';
        }

        return $state;
    }

    public function hydrateForForm(?Model $record): mixed
    {
        if ($record === null) {
            return '';
        }

        return '';
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if ($value === null || $value === '') {
            return ['value' => null, 'include' => false];
        }

        return [
            'value' => Hash::make((string) $value),
            'include' => true,
        ];
    }

    /** @return array<int, string> */
    public function getRules(?Model $record = null): array
    {
        $rules = parent::getRules($record);

        if ($record?->exists) {
            $rules = array_values(array_filter($rules, fn (mixed $rule): bool => $rule !== 'required'));
            $rules[] = 'nullable';
            $rules[] = 'min:8';
        } else {
            $rules[] = 'min:8';
        }

        if ($this->confirmed) {
            $rules[] = 'confirmed';
        }

        return array_values(array_unique($rules));
    }

    protected function meta(): array
    {
        return [
            'confirmed' => $this->confirmed,
        ];
    }
}
