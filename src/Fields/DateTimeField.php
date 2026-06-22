<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

final class DateTimeField extends DateField
{
    public static function make(string $name): static
    {
        return parent::make($name)->time(true);
    }
}
