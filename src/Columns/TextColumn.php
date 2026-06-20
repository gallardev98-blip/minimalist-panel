<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Columns;

final class TextColumn extends Column
{
    public function getType(): string
    {
        return 'text';
    }
}
