<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Fixtures;

use Panel\Minimalist\Resources\Spatie\RoleResource;

final class HostRoleResource extends RoleResource
{
    protected static ?string $slug = 'roles';
}
