<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Resources\Spatie\RoleResource;

final class HostRoleResource extends RoleResource
{
    protected static ?string $slug = 'roles';
}
