<?php

declare(strict_types=1);

namespace Panel\Minimalist\Columns;

final class TextColumn extends Column
{
    public function getType(): string
    {
        return 'text';
    }
}
