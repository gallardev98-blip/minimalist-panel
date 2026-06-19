<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Resources\Resource;
use Illuminate\Support\Facades\Gate;

final class PolicyResolver
{
    /** @param class-string<Resource> $resourceClass */
    public static function resolve(string $resourceClass): ?string
    {
        $explicit = $resourceClass::policy();

        if ($explicit !== null && class_exists($explicit)) {
            return $explicit;
        }

        $guessed = self::guessForModel($resourceClass::modelClass());

        return $guessed !== null && class_exists($guessed) ? $guessed : null;
    }

    /** @param class-string<Resource> $resourceClass */
    public static function isRegistered(string $resourceClass): bool
    {
        if ($resourceClass::policy() !== null && class_exists((string) $resourceClass::policy())) {
            return true;
        }

        $guessed = self::guessForModel($resourceClass::modelClass());

        if ($guessed !== null && class_exists($guessed)) {
            return true;
        }

        return Gate::getPolicyFor($resourceClass::modelClass()) !== null;
    }

    /** @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
    public static function guessForModel(string $modelClass): ?string
    {
        $namespace = rtrim((string) config('panel.policies.namespace', 'App\\Policies'), '\\');
        $policyClass = $namespace . '\\' . class_basename($modelClass) . 'Policy';

        return class_exists($policyClass) ? $policyClass : null;
    }

    /** @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
    public static function guessClassName(string $modelClass): string
    {
        $namespace = rtrim((string) config('panel.policies.namespace', 'App\\Policies'), '\\');

        return $namespace . '\\' . class_basename($modelClass) . 'Policy';
    }

    /** @param class-string<Resource> $resourceClass */
    public static function suggestedPath(string $resourceClass): string
    {
        $policyClass = self::guessClassName($resourceClass::modelClass());
        $relative = str_replace('App\\', 'app\\', $policyClass);
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);

        return app_path(str_replace('app' . DIRECTORY_SEPARATOR, '', $relative) . '.php');
    }
}
