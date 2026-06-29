<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Livewire\ResourceIndex;
use MyLaravelTools\Panel\Tests\Fixtures\Article;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
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

    public function test_empty_search_shows_contextual_message(): void
    {
        Article::query()->create(['title' => 'Visible', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('search', 'inexistente')
            ->assertSee(__('panel::panel.empty_search', ['query' => 'inexistente']))
            ->assertSee(__('panel::panel.clear_search'));
    }

    public function test_empty_list_shows_create_hint(): void
    {
        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->assertSee(__('panel::panel.empty_hint'))
            ->assertSee(__('panel::panel.create_resource', ['label' => 'Articles']));
    }

    public function test_confirm_modal_closes_on_cancel(): void
    {
        $article = Article::query()->create(['title' => 'Cancel Me', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('requestRowAction', 'delete', $article->id)
            ->assertSet('showConfirm', true)
            ->call('cancelConfirm')
            ->assertSet('showConfirm', false);

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'deleted_at' => null]);
    }

    public function test_muestra_chips_y_contador_de_resultados(): void
    {
        Article::query()->create(['title' => 'Alpha', 'published' => true]);
        Article::query()->create(['title' => 'Beta', 'published' => false]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('search', 'Alpha')
            ->assertSee(__('panel::panel.search_chip'))
            ->assertSee('Alpha')
            ->assertSee(__('panel::panel.results_range', ['from' => 1, 'to' => 1, 'total' => 1]));
    }

    public function test_quitar_criterio_elimina_busqueda(): void
    {
        Article::query()->create(['title' => 'Keep', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('search', 'Keep')
            ->call('quitarCriterio', 'search')
            ->assertSet('search', '')
            ->assertDontSee('panel-filter-chip__value">Keep</span>', false);
    }

    public function test_quitar_criterio_elimina_filtro(): void
    {
        Article::query()->create(['title' => 'Published', 'published' => true]);
        Article::query()->create(['title' => 'Draft', 'published' => false]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('filterValues.published', '1')
            ->call('quitarCriterio', 'published')
            ->assertSet('filterValues.published', '')
            ->assertSee('Published')
            ->assertSee('Draft');
    }

    public function test_abrir_registro_abre_modal_editar(): void
    {
        config(['panel.forms_in_modal' => true]);

        $article = Article::query()->create(['title' => 'Editable', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('abrirRegistro', $article->id)
            ->assertSet('showFormModal', true)
            ->assertSet('formRecordId', $article->id);
    }

    public function test_filas_no_clicables_si_esta_desactivado(): void
    {
        config(['panel.layout.index.clickable_rows' => false]);

        $article = Article::query()->create(['title' => 'Static', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('abrirRegistro', $article->id)
            ->assertSet('showFormModal', false);
    }

    public function test_limpiar_seleccion_vacia_checkboxes(): void
    {
        $article = Article::query()->create(['title' => 'Selected', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('selected', [$article->id])
            ->set('seleccionGlobal', true)
            ->call('limpiarSeleccion')
            ->assertSet('selected', [])
            ->assertSet('seleccionGlobal', false);
    }

    public function test_abrir_vista_rapida_muestra_registro(): void
    {
        $article = Article::query()->create(['title' => 'Vista Rápida', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('abrirVistaRapida', $article->id)
            ->assertSet('showVistaRapida', true)
            ->assertSet('vistaRapidaId', $article->id)
            ->assertSee(__('panel::panel.quick_view'))
            ->assertSee('Vista Rápida')
            ->call('cerrarVistaRapida')
            ->assertSet('showVistaRapida', false);
    }

    public function test_seleccionar_todos_los_resultados_activa_modo_global(): void
    {
        $a = Article::query()->create(['title' => 'A', 'published' => true]);
        Article::query()->create(['title' => 'B', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('perPage', 1)
            ->set('selected', [$a->id])
            ->call('seleccionarTodosLosResultados')
            ->assertSet('seleccionGlobal', true)
            ->assertSee(__('panel::panel.selected_all_matching', ['count' => 2]));
    }

    public function test_validacion_inline_marca_campo_vacio(): void
    {
        config(['panel.forms.validate_inline' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->call('openCreateFormModal')
            ->set('form.title', '')
            ->assertHasErrors(['form.title']);
    }

    public function test_bulk_preview_muestra_recuento(): void
    {
        $a = Article::query()->create(['title' => 'A', 'published' => true]);
        $b = Article::query()->create(['title' => 'B', 'published' => true]);

        Livewire::test(ResourceIndex::class, ['resource' => 'articles'])
            ->set('selected', [$a->id, $b->id])
            ->call('runBulkAction', 'delete')
            ->assertSet('showConfirm', true)
            ->assertSee(__('panel::panel.confirm_bulk_preview', [
                'count' => 2,
                'action' => __('panel::panel.bulk_delete'),
            ]));
    }
}
