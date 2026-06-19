@props([
    'name',
    'label',
    'type' => 'text',
    'autocomplete' => null,
])

<div>
    <label for="profile-{{ $name }}" class="panel-label">{{ $label }}</label>
    <input
        id="profile-{{ $name }}"
        type="{{ $type }}"
        wire:model="{{ $name }}"
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        class="panel-input mt-1.5"
    />
    @error($name)
        <p class="panel-field-error mt-1 text-sm">{{ $message }}</p>
    @enderror
</div>
