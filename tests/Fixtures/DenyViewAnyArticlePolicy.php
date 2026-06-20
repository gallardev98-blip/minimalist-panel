<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Policies\ResourcePolicy;

final class DenyViewAnyArticlePolicy extends ResourcePolicy
{
    public function viewAny($user): bool
    {
        return false;
    }
}
