<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Concerns;

use MyLaravelTools\Panel\Support\FieldPayload;
use MyLaravelTools\Panel\Support\FormSchema;
use MyLaravelTools\Panel\Support\PanelLayout;
use Illuminate\Database\Eloquent\Model;

trait ManagesResourceFormModal
{
    public bool $showFormModal = false;

    public ?int $formRecordId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    public function openCreateFormModal(): void
    {
        if (! $this->formsInModal()) {
            return;
        }

        abort_unless($this->resourceClass::authorize('create'), 403);

        $this->formRecordId = null;
        $this->form = FieldPayload::initialState(FormSchema::fields($this->resourceClass::form()));
        $this->showFormModal = true;
    }

    public function openEditFormModal(int|string $recordId): void
    {
        if (! $this->formsInModal()) {
            return;
        }

        $record = $this->resourceClass::findRecord(
            $recordId,
            $this->trashed === 'only',
        );

        abort_unless($this->resourceClass::authorize('update', $record), 403);

        $this->formRecordId = (int) $recordId;
        $this->form = FieldPayload::initialState(
            FormSchema::fields($this->resourceClass::form()),
            $record,
        );
        $this->showFormModal = true;
    }

    public function cancelFormModal(): void
    {
        $this->resetFormModal();
    }

    public function descartarBorradorFormulario(): void
    {
        if ($this->formRecordId !== null) {
            return;
        }

        $this->form = FieldPayload::initialState(FormSchema::fields($this->resourceClass::form()));
        $this->resetValidation();
        $this->notificarBorradorLimpiado();
    }

    public function updatedForm(mixed $value, string $key): void
    {
        if (! PanelLayout::validacionInlineForm() || ! $this->showFormModal) {
            return;
        }

        $this->validateOnly('form.'.$key);
    }

    public function saveFormModal(): void
    {
        if (! $this->showFormModal) {
            return;
        }

        $fields = FormSchema::fields($this->resourceClass::form());
        $record = $this->resolveFormModalRecord();
        $validated = $this->validate();
        $payload = FieldPayload::fromValidated($fields, $validated['form'], $record);

        if ($record === null) {
            abort_unless($this->resourceClass::authorize('create'), 403);

            $record = $this->resourceClass::modelClass()::query()->create($payload);
            FieldPayload::persistAfterSave($fields, $validated['form'], $record);
            $message = __('panel::panel.record_created');
        } else {
            abort_unless($this->resourceClass::authorize('update', $record), 403);

            $record->update($payload);
            FieldPayload::persistAfterSave($fields, $validated['form'], $record);
            $message = __('panel::panel.record_updated');
        }

        $this->toastSuccess($message);
        $this->notificarBorradorLimpiado();
        $this->resetFormModal();
    }

    protected function formsInModal(): bool
    {
        return (bool) config('panel.forms_in_modal', true);
    }

    /** @return array<string, mixed> */
    protected function formModalRules(): array
    {
        if (! $this->showFormModal) {
            return [];
        }

        $rules = $this->resourceClass::validationRules($this->resolveFormModalRecord());
        $prefixed = [];

        foreach ($rules as $key => $rule) {
            $prefixed['form.' . $key] = $rule;
        }

        return $prefixed;
    }

    /** @return array<string, string> */
    protected function formModalMessages(): array
    {
        if (! $this->showFormModal) {
            return [];
        }

        return $this->resourceClass::validationMessages();
    }

    private function resetFormModal(): void
    {
        $this->showFormModal = false;
        $this->formRecordId = null;
        $this->form = [];
        $this->resetValidation();
    }

    private function notificarBorradorLimpiado(): void
    {
        if (! PanelLayout::borradorFormulario()) {
            return;
        }

        $this->dispatch('panel-borrador-limpiado', slug: $this->resource, esNuevo: $this->formRecordId === null);
    }

    private function resolveFormModalRecord(): ?Model
    {
        if ($this->formRecordId === null) {
            return null;
        }

        return $this->resourceClass::findRecord($this->formRecordId, $this->trashed === 'only');
    }
}
