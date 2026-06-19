<?php

declare(strict_types=1);

namespace Panel\Minimalist\Fields;

final class BooleanField extends Field
{
    public function getType(): string
    {
        return 'boolean';
    }

    /** @return array<int, string> */
    public function getRules(?\Illuminate\Database\Eloquent\Model $record = null): array
    {
        return array_merge(parent::getRules($record), ['boolean']);
    }
}
