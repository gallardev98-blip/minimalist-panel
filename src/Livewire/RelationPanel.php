<?php



declare(strict_types=1);



namespace Panel\Minimalist\Livewire;



use Panel\Minimalist\Livewire\Concerns\ConfirmsPanelActions;
use Panel\Minimalist\Livewire\Concerns\DispatchesPanelToasts;

use Panel\Minimalist\Relations\RelationManager;

use Panel\Minimalist\Resources\Resource;

use Panel\Minimalist\Support\FieldPayload;

use Panel\Minimalist\Support\ResourceRegistry;

use Illuminate\Database\Eloquent\Model;

use Livewire\Component;

use Livewire\WithFileUploads;

use Livewire\WithPagination;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



final class RelationPanel extends Component

{

    use ConfirmsPanelActions;
    use DispatchesPanelToasts;

    use WithFileUploads;

    use WithPagination;



    public string $parentResource;



    public int $parentRecordId;



    public string $relation;



    public bool $showForm = false;



    public ?int $editingId = null;



    /** @var array<string, mixed> */

    public array $form = [];



    /** @var class-string<Resource> */

    private string $parentResourceClass;



    private Model $parentRecord;



    private RelationManager $manager;



    /** @var class-string<Resource> */

    private string $childResourceClass;



    private ?Model $editingRecord = null;



    public function mount(string $parentResource, int $parentRecordId, string $relation): void

    {

        $this->parentResource = $parentResource;

        $this->parentRecordId = $parentRecordId;

        $this->relation = $relation;



        $this->parentResourceClass = $this->resolveParentResource($parentResource);

        $this->parentRecord = $this->parentResourceClass::findRecord($parentRecordId);

        $this->manager = $this->resolveManager($relation);

        $this->childResourceClass = $this->manager->getResourceClass();

    }



    public function openCreateForm(): void

    {

        abort_unless($this->childResourceClass::authorize('create'), 403);



        $this->editingId = null;

        $this->editingRecord = null;

        $this->form = FieldPayload::initialState($this->manager->formFields($this->parentRecord));

        $this->showForm = true;

    }



    public function openEditForm(int $recordId): void

    {

        $record = $this->childResourceClass::findRecord($recordId);



        abort_unless($this->childResourceClass::authorize('update', $record), 403);



        $this->editingId = $recordId;

        $this->editingRecord = $record;

        $this->form = FieldPayload::initialState($this->manager->formFields($this->parentRecord), $record);

        $this->showForm = true;

    }



    public function cancelForm(): void

    {

        $this->resetForm();

    }



    public function requestDelete(int $recordId): void

    {

        $message = $this->manager->isBelongsToMany()

            ? __('panel::panel.confirm_detach')

            : __('panel::panel.confirm_delete');

        $this->askConfirm($message, 'delete', $recordId);

    }



    public function save(): void

    {

        $fields = $this->manager->formFields($this->parentRecord);

        $validated = $this->validate();

        $editingRecord = $this->resolveEditingRecord();

        $payload = FieldPayload::fromValidated($fields, $validated['form'], $editingRecord);



        if ($editingRecord === null) {

            abort_unless($this->childResourceClass::authorize('create'), 403);



            if ($this->manager->isBelongsToMany()) {

                $child = $this->childResourceClass::modelClass()::query()->create($payload);

                $this->parentRecord->{$this->relation}()->attach($child->getKey());

            } else {

                $payload[$this->manager->resolveForeignKey($this->parentRecord)] = $this->parentRecord->getKey();

                $this->childResourceClass::modelClass()::query()->create($payload);

            }



            $this->toastSuccess(__('panel::panel.related_created'));

        } else {

            abort_unless($this->childResourceClass::authorize('update', $editingRecord), 403);

            $editingRecord->update($payload);

            $this->toastSuccess(__('panel::panel.related_updated'));

        }



        $this->resetForm();

    }



    public function delete(int $recordId): void

    {

        $record = $this->childResourceClass::findRecord($recordId);



        abort_unless($this->childResourceClass::authorize('delete', $record), 403);



        if ($this->manager->isBelongsToMany()) {

            $this->parentRecord->{$this->relation}()->detach($recordId);



            if ($this->manager->shouldDeleteRelated()) {

                $record->delete();

                $this->toastSuccess(__('panel::panel.related_deleted'));

            } else {

                $this->toastSuccess(__('panel::panel.related_detached'));

            }

        } else {

            $record->delete();

            $this->toastSuccess(__('panel::panel.related_deleted'));

        }

    }



    public function render(): mixed

    {

        $pageName = 'rel-' . $this->relation;

        $records = $this->manager->query($this->parentRecord)->latest('id')->paginate(10, pageName: $pageName);



        return view('panel::livewire.relation-panel', [

            'title' => $this->manager->getTitle(),

            'columns' => $this->childResourceClass::table(),

            'formFields' => $this->manager->formFields($this->parentRecord),

            'formSchema' => $this->childResourceClass::form(),

            'records' => $records,

            'childResourceClass' => $this->childResourceClass,

            'canCreate' => $this->childResourceClass::authorize('create'),

            'isBelongsToMany' => $this->manager->isBelongsToMany(),

            'showConfirm' => $this->showConfirm,

            'confirmMessage' => $this->confirmMessage,

        ]);

    }



    /** @return array<string, mixed> */

    protected function rules(): array

    {

        $rules = $this->childResourceClass::validationRules($this->resolveEditingRecord());

        $prefixed = [];



        foreach ($this->manager->formFields($this->parentRecord) as $field) {

            $name = $field->getName();

            if (isset($rules[$name])) {

                $prefixed['form.' . $name] = $rules[$name];

            }

        }



        return $prefixed;

    }



    /** @return array<string, string> */

    protected function messages(): array

    {

        return $this->childResourceClass::validationMessages();

    }



    /** @return class-string<Resource> */

    private function resolveParentResource(string $slug): string

    {

        $resourceClass = app(ResourceRegistry::class)->findBySlug($slug);



        if ($resourceClass === null) {

            throw new NotFoundHttpException("Resource [{$slug}] not found.");

        }



        return $resourceClass;

    }



    private function resolveManager(string $relation): RelationManager

    {

        foreach ($this->parentResourceClass::relations() as $manager) {

            if ($manager->getRelationship() === $relation) {

                return $manager;

            }

        }



        throw new NotFoundHttpException("Relation [{$relation}] not found.");

    }



    private function resetForm(): void

    {

        $this->showForm = false;

        $this->editingId = null;

        $this->editingRecord = null;

        $this->form = [];

        $this->resetValidation();

    }



    private function resolveEditingRecord(): ?Model

    {

        if ($this->editingId === null) {

            return null;

        }



        return $this->childResourceClass::findRecord($this->editingId);

    }

}

