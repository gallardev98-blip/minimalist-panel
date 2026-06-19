<?php

declare(strict_types=1);

namespace Panel\Minimalist\Columns;

use Illuminate\Database\Eloquent\Model;

final class BelongsToColumn extends Column
{
    protected ?string $relationship = null;

    protected string $displayColumn = 'name';

    public function relationship(string $relationship, string $displayColumn = 'name'): static
    {
        $this->relationship = $relationship;
        $this->displayColumn = $displayColumn;

        return $this;
    }

    public function getType(): string
    {
        return 'belongs-to';
    }

    public function resolve(Model $record): mixed
    {
        if ($this->relationship === null) {
            return parent::resolve($record);
        }

        $related = $record->{$this->relationship};

        return $related?->{$this->displayColumn};
    }
}
