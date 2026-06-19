<div>
    <x-panel::page-header class="mb-6">
        <a href="{{ route('panel.resources.index', ['resource' => $resourceSlug]) }}" class="panel-back-link" wire:navigate wire:navigate.hover>
            <x-panel::icon name="arrow-left" class="h-4 w-4" />
            {{ __('panel::panel.back_to', ['label' => $resourceLabel]) }}
        </a>
        <h1 class="mt-3">
            {{ $isEditing ? __('panel::panel.edit') : __('panel::panel.create') }} {{ $resourceLabel }}
        </h1>
    </x-panel::page-header>

    <form wire:submit="save" class="panel-form-card">
        <div class="panel-form-card-body">
            @include('panel::partials.form-schema', ['formSchema' => $formSchema])
        </div>

        <div class="panel-form-footer">
            <button type="submit" class="panel-btn panel-btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ __('panel::panel.save') }}</span>
                <span wire:loading wire:target="save">{{ __('panel::panel.loading') }}</span>
            </button>
            <a href="{{ route('panel.resources.index', ['resource' => $resourceSlug]) }}" class="panel-btn panel-btn-ghost" wire:navigate wire:navigate.hover>
                {{ __('panel::panel.cancel') }}
            </a>
        </div>
    </form>
</div>
