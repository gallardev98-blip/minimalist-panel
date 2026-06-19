<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Feature;

use Panel\Minimalist\Livewire\ResourceIndex;
use Panel\Minimalist\Tests\Fixtures\Article;
use Panel\Minimalist\Tests\Fixtures\ArticleResource;
use Panel\Minimalist\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

final class ResourceIndexTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app['config']->set('panel.resources', [ArticleResource::class]);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function defineRoutes($router): void
    {
        $router->get('/admin/resources/{resource}', ResourceIndex::class)->name('panel.resources.index');
    }

    public function test_it_lists_searchable_records(): void
    {
        Article::query()->create(['title' => 'Laravel Panel', 'published' => true]);
        Article::query()->create(['title' => 'Other', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('search', 'Laravel')
            ->assertSee('Laravel Panel')
            ->assertDontSee('Other');
    }

    public function test_it_deletes_record_via_row_action(): void
    {
        $article = Article::query()->create(['title' => 'To Delete', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('runRowActionWithoutConfirm', 'delete', $article->id)
            ->assertDispatched('panel-toast');

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_confirm_modal_opens_before_delete(): void
    {
        $article = Article::query()->create(['title' => 'Confirm Me', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('requestRowAction', 'delete', $article->id)
            ->assertSet('showConfirm', true)
            ->assertSet('pendingAction', 'row')
            ->call('executeConfirm')
            ->assertSet('showConfirm', false);

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_it_creates_record_via_form_modal(): void
    {
        config(['panel.forms_in_modal' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('openCreateFormModal')
            ->assertSet('showFormModal', true)
            ->set('form.title', 'Modal Article')
            ->call('saveFormModal')
            ->assertSet('showFormModal', false)
            ->assertDispatched('panel-toast');

        $this->assertDatabaseHas('articles', ['title' => 'Modal Article']);
    }

    public function test_it_edits_record_via_form_modal(): void
    {
        config(['panel.forms_in_modal' => true]);

        $article = Article::query()->create(['title' => 'Before', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('openEditFormModal', $article->id)
            ->assertSet('showFormModal', true)
            ->assertSet('formRecordId', $article->id)
            ->set('form.title', 'After')
            ->call('saveFormModal')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'After']);
    }
}
