<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

abstract class Field
{
    protected string $name;

    protected ?string $label = null;

    /** @var array<int, string> */
    protected array $rules = [];

    /** @var array<string, string> */
    protected array $validationMessages = [];

    protected bool $required = false;

    protected bool $disabled = false;

    protected mixed $default = null;

    /** @var array{table: string, column: string}|null */
    protected ?array $uniqueConstraint = null;

    final public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = str($name)->headline()->toString();
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /** @param array<int, string>|string $rules */
    public function rules(array|string $rules): static
    {
        $this->rules = is_array($rules) ? $rules : [$rules];

        return $this;
    }

    public function unique(string $table, ?string $column = null): static
    {
        $this->uniqueConstraint = [
            'table' => $table,
            'column' => $column ?? $this->name,
        ];

        return $this;
    }

    /** @param array<string, string> $messages */
    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    /** @return array<int, string|\Illuminate\Contracts\Validation\ValidationRule> */
    public function getRules(?Model $record = null): array
    {
        $rules = $this->rules;

        if ($this->required && ! in_array('required', $rules, true)) {
            $rules[] = 'required';
        }

        if ($this->uniqueConstraint !== null) {
            $uniqueRule = Rule::unique(
                $this->uniqueConstraint['table'],
                $this->uniqueConstraint['column'],
            );

            if ($record?->exists) {
                $uniqueRule->ignore($record->getKey());
            }

            $rules[] = $uniqueRule;
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    public function augmentFormState(array $state): array
    {
        return $state;
    }

    public function hydrateForForm(?Model $record): mixed
    {
        if ($record === null) {
            return $this->getDefault() ?? ($this->getType() === 'boolean' ? false : null);
        }

        return data_get($record, $this->name);
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        return [
            'value' => $value,
            'include' => true,
        ];
    }

    public function afterSave(Model $record, mixed $value): void
    {
    }

    abstract public function getType(): string;

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'required' => $this->required,
            'disabled' => $this->disabled,
            'default' => $this->default,
            'meta' => $this->meta(),
        ];
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [];
    }
}
