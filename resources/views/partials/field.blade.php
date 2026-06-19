@php
    $name = $field->getName();
    $type = $field->getType();
    $meta = $field->toArray()['meta'] ?? [];
@endphp

<div>
    <label for="field-{{ $name }}" class="panel-label">
        {{ $field->getLabel() }}
        @if ($field->isRequired())
            <span class="text-[rgb(var(--panel-danger))]">*</span>
        @endif
    </label>

    @switch($type)
        @case('textarea')
            <textarea
                id="field-{{ $name }}"
                wire:model="form.{{ $name }}"
                rows="{{ $meta['rows'] ?? 4 }}"
                @disabled($field->isDisabled())
                placeholder="{{ $meta['placeholder'] ?? '' }}"
                class="panel-input"
            ></textarea>
            @break

        @case('rich-text')
            <div
                x-data="{
                    sync() { $wire.set('form.{{ $name }}', $refs.editor.innerHTML) },
                    exec(command) { document.execCommand(command, false, null); $refs.editor.focus(); this.sync() },
                }"
                wire:ignore
            >
                <div class="mb-2 flex flex-wrap gap-1">
                    <button type="button" class="panel-btn panel-btn-ghost !px-2 !py-1 text-xs font-bold" @click="exec('bold')">B</button>
                    <button type="button" class="panel-btn panel-btn-ghost !px-2 !py-1 text-xs italic" @click="exec('italic')">I</button>
                    <button type="button" class="panel-btn panel-btn-ghost !px-2 !py-1 text-xs underline" @click="exec('underline')">U</button>
                    <button type="button" class="panel-btn panel-btn-ghost !px-2 !py-1 text-xs" @click="exec('insertUnorderedList')">•</button>
                </div>
                <div
                    x-ref="editor"
                    contenteditable="true"
                    @input="sync()"
                    x-init="$refs.editor.innerHTML = @js($form[$name] ?? '')"
                    class="panel-input min-h-[8rem] prose prose-sm max-w-none dark:prose-invert"
                ></div>
            </div>
            @break

        @case('boolean')
            <label class="inline-flex items-center gap-2">
                <input
                    id="field-{{ $name }}"
                    type="checkbox"
                    wire:model="form.{{ $name }}"
                    @disabled($field->isDisabled())
                    class="panel-checkbox rounded"
                >
                <span class="panel-muted text-sm">{{ __('panel::panel.enabled') }}</span>
            </label>
            @break

        @case('select')
        @case('belongs-to')
            <select
                id="field-{{ $name }}"
                wire:model="form.{{ $name }}"
                @disabled($field->isDisabled())
                class="panel-input"
            >
                <option value="">{{ __('panel::panel.select') }}</option>
                @foreach (($meta['options'] ?? []) as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('multi-select')
        @case('roles')
        @case('permissions')
            <select
                id="field-{{ $name }}"
                wire:model="form.{{ $name }}"
                multiple
                @disabled($field->isDisabled())
                class="panel-input min-h-[6rem]"
            >
                @foreach (($meta['options'] ?? []) as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('password')
            <input
                id="field-{{ $name }}"
                type="password"
                wire:model="form.{{ $name }}"
                @disabled($field->isDisabled())
                class="panel-input"
            >
            @if (($meta['confirmed'] ?? false))
                <input
                    type="password"
                    wire:model="form.{{ $name }}_confirmation"
                    placeholder="{{ __('panel::panel.confirm_password') }}"
                    class="panel-input mt-2"
                >
            @endif
            @break

        @case('image')
            @if (! empty($form[$name] ?? null) && ! is_object($form[$name] ?? null))
                <img src="{{ Storage::disk($meta['disk'] ?? 'public')->url($form[$name]) }}" alt="" class="mb-3 h-24 w-24 rounded-lg object-cover ring-1 ring-[rgb(var(--panel-border))]">
            @endif
            <input
                id="field-{{ $name }}"
                type="file"
                wire:model="form.{{ $name }}"
                accept="image/*"
                @disabled($field->isDisabled())
                class="block w-full text-sm text-[rgb(var(--panel-muted))] file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[rgb(var(--panel-primary))] file:px-4 file:py-2 file:text-sm file:font-medium file:text-[rgb(var(--panel-primary-fg))] hover:file:brightness-110"
            >
            <div wire:loading wire:target="form.{{ $name }}" class="panel-muted mt-2 text-sm">{{ __('panel::panel.uploading') }}</div>
            @break

        @case('file')
            @if (! empty($form[$name] ?? null) && ! is_object($form[$name] ?? null))
                <a
                    href="{{ Storage::disk($meta['disk'] ?? 'public')->url($form[$name]) }}"
                    target="_blank"
                    class="panel-action-link mb-3 inline-flex items-center gap-1 text-sm"
                >
                    <x-panel::icon name="file" class="h-4 w-4" />
                    {{ __('panel::panel.download_file') }}
                </a>
            @endif
            <input
                id="field-{{ $name }}"
                type="file"
                wire:model="form.{{ $name }}"
                @if (! empty($meta['acceptedMimes']))
                    accept="{{ collect($meta['acceptedMimes'])->map(fn ($m) => '.' . $m)->implode(',') }}"
                @endif
                @disabled($field->isDisabled())
                class="block w-full text-sm text-[rgb(var(--panel-muted))] file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[rgb(var(--panel-primary))] file:px-4 file:py-2 file:text-sm file:font-medium file:text-[rgb(var(--panel-primary-fg))] hover:file:brightness-110"
            >
            <div wire:loading wire:target="form.{{ $name }}" class="panel-muted mt-2 text-sm">{{ __('panel::panel.uploading') }}</div>
            @break

        @case('number')
            <input
                id="field-{{ $name }}"
                type="number"
                wire:model="form.{{ $name }}"
                min="{{ $meta['min'] ?? '' }}"
                max="{{ $meta['max'] ?? '' }}"
                step="{{ $meta['step'] ?? 'any' }}"
                @disabled($field->isDisabled())
                class="panel-input"
            >
            @break

        @default
            <input
                id="field-{{ $name }}"
                type="{{ $type }}"
                wire:model="form.{{ $name }}"
                @disabled($field->isDisabled())
                placeholder="{{ $meta['placeholder'] ?? '' }}"
                maxlength="{{ $meta['maxLength'] ?? '' }}"
                class="panel-input"
            >
    @endswitch

    @error('form.' . $name)
        <p class="mt-1 text-sm text-[rgb(var(--panel-danger))]">{{ $message }}</p>
    @enderror
</div>
