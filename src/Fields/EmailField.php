<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

final class EmailField extends TextField
{
    public function getType(): string
    {
        return 'email';
    }

    /** @return array<int, string> */
    public function getRules(?\Illuminate\Database\Eloquent\Model $record = null): array
    {
        return array_merge(parent::getRules($record), ['email']);
    }
}
