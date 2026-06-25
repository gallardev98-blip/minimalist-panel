<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class Package
{
    public const NAME = 'mylaraveltools/panel';

    public const DISPLAY_NAME = 'Panel';

    public const VERSION = '0.39.2';

    public static function vendorPath(): string
    {
        return 'vendor/'.self::NAME;
    }
}
