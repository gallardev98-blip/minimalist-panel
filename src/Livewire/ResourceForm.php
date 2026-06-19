<?php



declare(strict_types=1);



namespace Panel\Minimalist\Livewire;



use Panel\Minimalist\Livewire\Concerns\DispatchesPanelToasts;

use Panel\Minimalist\Livewire\Concerns\InteractsWithPanelResource;

use Panel\Minimalist\Resources\Resource;

use Panel\Minimalist\Support\FieldPayload;

use Panel\Minimalist\Support\FormSchema;

use Panel\Minimalist\Support\ResourceRegistry;

use Illuminate\Database\Eloquent\Model;

use Livewire\Attributes\Layout;

use Livewire\Component;

use Livewire\WithFileUploads;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



#[Layout('panel::layouts.app')]

final class ResourceForm extends Component

{

    use DispatchesPanelToasts;

    use InteractsWithPanelResource;

    use WithFileUploads;



    public string $resource;



    public ?int $recordId = null;



    /** @var array<string, mixed> */

    public array $form = [];



    private ?Model $record = null;



    public function mount(string $resource, int|string|null $record = null): void

    {

        $this->resource = $resource;



        if ($record !== null) {

            $resourceClass = app(ResourceRegistry::class)->findBySlug($resource);



            if ($resourceClass === null) {

                throw new NotFoundHttpException("Resource [{$resource}] not found.");

            }



            $this->record = $resourceClass::findRecord($record);

            $this->recordId = (int) $record;

            $this->resourceClass = $this->resolveResource($resource, 'update', $this->record);

        } else {

            $this->resourceClass = $this->resolveResource($resource, 'create');

        }



        $this->form = FieldPayload::initialState(FormSchema::fields($this->resourceClass::form()), $this->record);

    }



    public function save(): void

    {

        $resourceClass = $this->resourceClass;

        $fields = FormSchema::fields($resourceClass::form());



        $validated = $this->validate();

        $payload = FieldPayload::fromValidated($fields, $validated['form'], $this->record);



        if ($this->record === null) {

            $resourceClass::modelClass()::query()->create($payload);

            $message = __('panel::panel.record_created');

        } else {

            $this->record->update($payload);

            $message = __('panel::panel.record_updated');

        }



        $this->toastSuccess($message);



        $this->redirectRoute('panel.resources.index', ['resource' => $this->resource], navigate: true);

    }



    public function render(): mixed

    {

        /** @var class-string<Resource> $resourceClass */

        $resourceClass = $this->resourceClass;

        $isEditing = $this->record !== null;

        $formSchema = $resourceClass::form();



        return view('panel::livewire.resource-form', array_merge($this->sharedPanelData(), [

            'resourceSlug' => $this->resource,

            'resourceLabel' => $resourceClass::label(),

            'formSchema' => $formSchema,

            'fields' => FormSchema::fields($formSchema),

            'hasSections' => FormSchema::hasSections($formSchema),

            'hasTabs' => FormSchema::hasTabs($formSchema),

            'isEditing' => $isEditing,

        ]))->title(($isEditing ? __('panel::panel.edit') . ' ' : __('panel::panel.create') . ' ') . $resourceClass::label());

    }



    /** @return array<string, mixed> */

    protected function rules(): array

    {

        $rules = $this->resourceClass::validationRules($this->record);

        $prefixed = [];



        foreach ($rules as $key => $rule) {

            $prefixed['form.' . $key] = $rule;

        }



        return $prefixed;

    }



    /** @return array<string, string> */

    protected function messages(): array

    {

        return $this->resourceClass::validationMessages();

    }

}

