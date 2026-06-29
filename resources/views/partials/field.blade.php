@php
    $name = $field->getName();
    $type = $field->getType();
    $meta = $field->toArray()['meta'] ?? [];
    $vistaCampo = ($field instanceof \MyLaravelTools\Panel\Fields\CustomField && $field->getView())
        ? $field->getView()
        : \MyLaravelTools\Panel\Facades\PanelExtensions::vistaCampo($type);
@endphp

@if ($vistaCampo)
    @include($vistaCampo, ['field' => $field, 'form' => $form])
@else
<div class="panel-field">
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
                <div class="mb-2 panel-rich-text-toolbar">
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
                    class="panel-input panel-rich-text-editor prose prose-sm max-w-none dark:prose-invert"
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
            <x-panel::searchable-select
                :id="'field-' . $name"
                wire:model="form.{{ $name }}"
                :options="['' => __('panel::panel.select')] + ($meta['options'] ?? [])"
                :disabled="$field->isDisabled()"
            />
            @break

        @case('multi-select')
        @case('roles')
        @case('permissions')
            <x-panel::searchable-select
                :id="'field-' . $name"
                wire:model="form.{{ $name }}"
                :options="$meta['options'] ?? []"
                multiple
                :disabled="$field->isDisabled()"
            />
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
                class="panel-file-input"
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
                class="panel-file-input"
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

        @case('color')
            <input
                id="field-{{ $name }}"
                type="color"
                wire:model="form.{{ $name }}"
                @disabled($field->isDisabled())
                class="panel-input h-10 w-20 cursor-pointer p-1"
            >
            @break

        @case('key-value')
            <div
                class="space-y-2"
                x-data="{
                    filas: [],
                    initFilas() {
                        const datos = @js(is_array($form[$name] ?? null) ? $form[$name] : []);
                        this.filas = Object.entries(datos).map(([c, v]) => ({ c, v }));
                        if (this.filas.length === 0) this.filas = [{ c: '', v: '' }];
                    },
                    sync() {
                        const out = {};
                        this.filas.forEach(f => { if (f.c) out[f.c] = f.v; });
                        $wire.set('form.{{ $name }}', out);
                    }
                }"
                x-init="initFilas()"
            >
                <template x-for="(fila, i) in filas" :key="i">
                    <div class="flex gap-2">
                        <input type="text" x-model="fila.c" @input="sync()" placeholder="{{ __('panel::panel.key') }}" class="panel-input flex-1">
                        <input type="text" x-model="fila.v" @input="sync()" placeholder="{{ __('panel::panel.value') }}" class="panel-input flex-1">
                        <button type="button" @click="filas.splice(i, 1); sync()" class="panel-btn panel-btn-ghost !px-2" aria-label="{{ __('panel::panel.remove') }}">×</button>
                    </div>
                </template>
                <button type="button" @click="filas.push({ c: '', v: '' })" class="panel-btn panel-btn-ghost text-sm">+ {{ __('panel::panel.add_row') }}</button>
            </div>
            @break

        @case('repeater')
            @php
                $columnas = $meta['columns'] ?? ['value' => __('panel::panel.value')];
                $maxFilas = (int) ($meta['max'] ?? 20);
            @endphp
            <div
                class="space-y-3"
                x-data="{
                    filas: [],
                    columnas: @js($columnas),
                    max: {{ $maxFilas }},
                    initFilas() {
                        const datos = @js(is_array($form[$name] ?? null) ? $form[$name] : []);
                        this.filas = Array.isArray(datos) ? datos.map(f => ({ ...f })) : [];
                        if (this.filas.length === 0) this.anadir();
                    },
                    filaVacia() {
                        const f = {};
                        Object.keys(this.columnas).forEach(k => f[k] = '');
                        return f;
                    },
                    sync() {
                        $wire.set('form.{{ $name }}', this.filas.filter(f => Object.values(f).some(v => String(v).trim() !== '')));
                    },
                    anadir() {
                        if (this.filas.length >= this.max) return;
                        this.filas.push(this.filaVacia());
                        this.sync();
                    },
                    quitar(i) {
                        this.filas.splice(i, 1);
                        this.sync();
                    }
                }"
                x-init="initFilas()"
            >
                <template x-for="(fila, i) in filas" :key="i">
                    <div class="panel-repeater-item space-y-2">
                        <div class="grid gap-2 sm:grid-cols-2">
                            <template x-for="(etiqueta, clave) in columnas" :key="clave">
                                <div>
                                    <label class="panel-label text-xs" x-text="etiqueta"></label>
                                    <input type="text" class="panel-input" x-model="fila[clave]" @input="sync()">
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="quitar(i)" class="panel-btn panel-btn-ghost text-xs">× {{ __('panel::panel.remove_row') }}</button>
                    </div>
                </template>
                <button type="button" @click="anadir()" class="panel-btn panel-btn-ghost text-sm">+ {{ __('panel::panel.add_row') }}</button>
            </div>
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
        <p class="panel-field-error mt-1 text-sm">{{ $message }}</p>
    @enderror
</div>
@endif
