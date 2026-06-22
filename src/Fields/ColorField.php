<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

final class ColorField extends Field
{
    public function getType(): string
    {
        return 'color';
    }
}
