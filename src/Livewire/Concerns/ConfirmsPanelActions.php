<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire\Concerns;

trait ConfirmsPanelActions
{
    public bool $showConfirm = false;

    public string $confirmMessage = '';

    public ?string $pendingAction = null;

    public int|string|null $pendingRecordId = null;

    public ?string $pendingBulkAction = null;

    public ?string $pendingRowAction = null;

    public function cancelConfirm(): void
    {
        $this->showConfirm = false;
        $this->pendingAction = null;
        $this->pendingRecordId = null;
        $this->pendingBulkAction = null;
        $this->pendingRowAction = null;
        $this->confirmMessage = '';
    }

    public function executeConfirm(): void
    {
        match ($this->pendingAction) {
            'delete' => $this->delete($this->pendingRecordId),
            'forceDelete' => $this->forceDelete($this->pendingRecordId),
            'bulk' => $this->runBulkActionWithoutConfirm($this->pendingBulkAction ?? ''),
            'row' => $this->runRowActionWithoutConfirm($this->pendingRowAction ?? '', $this->pendingRecordId),
            default => null,
        };

        $this->cancelConfirm();
    }

    protected function askConfirm(string $message, string $action, int|string|null $recordId = null, ?string $bulkAction = null, ?string $rowAction = null): void
    {
        $this->confirmMessage = $message;
        $this->pendingAction = $action;
        $this->pendingRecordId = $recordId;
        $this->pendingBulkAction = $bulkAction;
        $this->pendingRowAction = $rowAction;
        $this->showConfirm = true;
    }
}
