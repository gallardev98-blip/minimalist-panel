<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Policies\ResourcePolicy;
use MyLaravelTools\Panel\Support\PolicyResolver;
use MyLaravelTools\Panel\Support\ResourceAuthorizer;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\Fixtures\AllowViewAnyArticlePolicy;
use MyLaravelTools\Panel\Tests\Fixtures\DenyViewAnyArticlePolicy;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Gate;

final class ResourceAuthorizerTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app['config']->set('panel.resources', [ArticleResource::class]);
        $app['config']->set('panel.policies.auto_register', false);
    }

    public function test_it_allows_when_no_policy_is_registered(): void
    {
        $this->actingAs(new GenericUser(['id' => 1]));

        $this->assertTrue(
            app(ResourceAuthorizer::class)->authorize(ArticleResource::class, 'viewAny'),
        );
    }

    public function test_it_denies_when_policy_denies(): void
    {
        $this->actingAs(new GenericUser(['id' => 1]));

        Gate::policy(Article::class, DenyViewAnyArticlePolicy::class);

        $this->assertFalse(
            app(ResourceAuthorizer::class)->authorize(ArticleResource::class, 'viewAny'),
        );
    }

    public function test_it_denies_policy_when_user_is_guest(): void
    {
        Gate::policy(Article::class, DenyViewAnyArticlePolicy::class);

        $this->assertFalse(
            app(ResourceAuthorizer::class)->authorize(ArticleResource::class, 'viewAny'),
        );
    }

    public function test_resource_hooks_and_policy_must_both_allow(): void
    {
        $this->actingAs(new GenericUser(['id' => 1]));

        Gate::policy(Article::class, AllowViewAnyArticlePolicy::class);

        $this->assertTrue(
            app(ResourceAuthorizer::class)->authorize(ArticleResource::class, 'viewAny'),
        );
    }

    public function test_policy_resolver_guesses_class_name(): void
    {
        $this->assertSame(
            'App\\Policies\\ArticlePolicy',
            PolicyResolver::guessClassName(Article::class),
        );
    }
}
