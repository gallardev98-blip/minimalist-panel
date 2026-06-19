<?php

declare(strict_types=1);

namespace Panel\Minimalist\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Base policy for panel resources. Override only the abilities you need.
 *
 * By default all abilities deny access — opt in explicitly in your policy.
 * Parameters stay untyped — PHP does not allow narrowing types when overriding.
 * Use instanceof in the method body if you need your User or Model class.
 */
abstract class ResourcePolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return false;
    }

    public function view($user, $record): bool
    {
        return false;
    }

    public function create($user): bool
    {
        return false;
    }

    public function update($user, $record): bool
    {
        return false;
    }

    public function delete($user, $record): bool
    {
        return false;
    }

    public function restore($user, $record): bool
    {
        return $this->delete($user, $record);
    }

    public function forceDelete($user, $record): bool
    {
        return $this->delete($user, $record);
    }
}
