@php
    use MyLaravelTools\Panel\Forms\Section;
    use MyLaravelTools\Panel\Support\FormSchema;
@endphp

@if ($hasTabs ?? FormSchema::hasTabs($formSchema))
    @include('panel::partials.form-tabs', ['formSchema' => $formSchema])
@else
    @include('panel::partials.form-schema-items', ['formSchema' => $formSchema])
@endif
