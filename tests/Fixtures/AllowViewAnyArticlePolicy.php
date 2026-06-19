<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Fixtures;

use Panel\Minimalist\Policies\ResourcePolicy;

final class AllowViewAnyArticlePolicy extends ResourcePolicy
{
    public function viewAny($user): bool
    {
        return true;
    }
}
