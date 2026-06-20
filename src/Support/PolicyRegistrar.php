<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Resources\Resource;
use Illuminate\Support\Facades\Gate;

final class PolicyRegistrar
{
    public function __construct(
        private readonly ResourceRegistry $registry,
    ) {}

    public function register(): void
    {
        if (! config('panel.policies.auto_register', true)) {
            return;
        }

        foreach ($this->registry->all() as $resourceClass) {
            $this->registerResourcePolicy($resourceClass);
        }
    }

    /** @param class-string<Resource> $resourceClass */
    private function registerResourcePolicy(string $resourceClass): void
    {
        $policyClass = PolicyResolver::resolve($resourceClass);

        if ($policyClass === null) {
            return;
        }

        Gate::policy($resourceClass::modelClass(), $policyClass);
    }
}
