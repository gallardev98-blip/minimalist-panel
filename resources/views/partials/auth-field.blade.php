@props([
    'name',
    'label',
    'type' => 'text',
    'icon' => 'circle',
    'autocomplete' => null,
    'autofocus' => false,
])

<div class="panel-auth-field">
    <label for="{{ $name }}" class="panel-auth-label">{{ $label }}</label>
    <div class="panel-auth-input-wrap">
        <span class="panel-auth-input-icon" aria-hidden="true">
            <x-panel::icon :name="$icon" class="h-4 w-4" />
        </span>
        <input
            id="{{ $name }}"
            type="{{ $type }}"
            wire:model="{{ $name }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($autofocus) autofocus @endif
            class="panel-auth-input"
            required
        />
    </div>
    @error($name)
        <p class="panel-auth-error" role="alert">{{ $message }}</p>
    @enderror
</div>
