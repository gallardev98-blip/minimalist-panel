<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Resources\Resource;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

final class ResourceAuthorizer
{
    public function authorize(string $resourceClass, string $ability, ?Model $record = null): bool
    {
        if (! $this->resourceAllows($resourceClass, $ability, $record)) {
            return false;
        }

        return $this->policyAllows($resourceClass, $ability, $record);
    }

    public function user(): ?Authenticatable
    {
        return auth(config('panel.guard', 'web'))->user();
    }

    /** @param class-string<Resource> $resourceClass */
    public function hasPolicy(string $resourceClass): bool
    {
        return PolicyResolver::isRegistered($resourceClass);
    }

    /** @param class-string<Resource> $resourceClass */
    private function resourceAllows(string $resourceClass, string $ability, ?Model $record): bool
    {
        return match ($ability) {
            'viewAny' => $resourceClass::canViewAny(),
            'view' => $record !== null && $resourceClass::canView($record),
            'create' => $resourceClass::canCreate(),
            'update' => $record !== null && $resourceClass::canEdit($record),
            'delete' => $record !== null && $resourceClass::canDelete($record),
            'restore' => $record !== null && $resourceClass::canRestore($record),
            'forceDelete' => $record !== null && $resourceClass::canForceDelete($record),
            default => false,
        };
    }

    /** @param class-string<Resource> $resourceClass */
    private function policyAllows(string $resourceClass, string $ability, ?Model $record): bool
    {
        if (! PolicyResolver::isRegistered($resourceClass)) {
            return true;
        }

        $user = $this->user();

        if ($user === null) {
            return false;
        }

        $modelClass = $resourceClass::modelClass();

        return match ($ability) {
            'viewAny' => Gate::forUser($user)->allows('viewAny', $modelClass),
            'view' => $record !== null && Gate::forUser($user)->allows('view', $record),
            'create' => Gate::forUser($user)->allows('create', $modelClass),
            'update' => $record !== null && Gate::forUser($user)->allows('update', $record),
            'delete' => $record !== null && Gate::forUser($user)->allows('delete', $record),
            'restore' => $record !== null && Gate::forUser($user)->allows('restore', $record),
            'forceDelete' => $record !== null && Gate::forUser($user)->allows('forceDelete', $record),
            default => false,
        };
    }
}
