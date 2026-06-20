<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;

final class BelongsToField extends Field
{
    /** @var class-string<Model> */
    protected string $relatedModel;

    protected string $titleColumn = 'name';

    protected ?string $relationship = null;

    /** @param class-string<Model> $model */
    public function relationship(string $model, string $titleColumn = 'name'): static
    {
        $this->relatedModel = $model;
        $this->titleColumn = $titleColumn;

        return $this;
    }

    public function getType(): string
    {
        return 'belongs-to';
    }

    /** @return array<string|int, string> */
    public function resolveOptions(): array
    {
        if (! isset($this->relatedModel)) {
            throw new \InvalidArgumentException('BelongsToField requires ->relationship(Model::class) configuration.');
        }

        return $this->relatedModel::query()
            ->orderBy($this->titleColumn)
            ->pluck($this->titleColumn, 'id')
            ->all();
    }

    /** @return array<int, string> */
    public function getRules(?Model $record = null): array
    {
        if (! isset($this->relatedModel)) {
            return parent::getRules($record);
        }

        $table = (new $this->relatedModel())->getTable();

        return array_merge(parent::getRules($record), ['exists:' . $table . ',id']);
    }

    protected function meta(): array
    {
        return [
            'options' => $this->resolveOptions(),
        ];
    }
}
