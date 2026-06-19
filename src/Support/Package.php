<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

final class Package
{
    public const NAME = 'gallardev/minimalist';

    public const DISPLAY_NAME = 'Minimalist';

    public const VERSION = '0.14.0';

    public static function vendorPath(): string
    {
        return 'vendor/'.self::NAME;
    }
}
