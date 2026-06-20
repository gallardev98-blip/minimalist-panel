<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Fixtures;

use MyLaravelTools\Panel\Policies\ResourcePolicy;

final class AllowViewAnyArticlePolicy extends ResourcePolicy
{
    public function viewAny($user): bool
    {
        return true;
    }
}
