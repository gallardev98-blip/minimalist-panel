<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class Package
{
    public const NAME = 'mylaraveltools/minimalist';

    public const DISPLAY_NAME = 'Minimalist';

    public const VERSION = '0.16.0';

    public static function vendorPath(): string
    {
        return 'vendor/'.self::NAME;
    }
}
