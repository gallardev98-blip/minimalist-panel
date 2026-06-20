@php
    use MyLaravelTools\Panel\Forms\Section;
@endphp

@foreach ($formSchema as $item)
    @if ($item instanceof Section)
        <div class="panel-form-section">
            <div class="panel-form-section-header">
                <h2 class="panel-form-section-title">{{ $item->getTitle() }}</h2>
                @if ($item->getDescription())
                    <p class="panel-form-section-description">{{ $item->getDescription() }}</p>
                @endif
            </div>
            <div class="panel-form-section-body space-y-5">
                @foreach ($item->getFields() as $field)
                    @include('panel::partials.field', ['field' => $field])
                @endforeach
            </div>
        </div>
    @else
        @include('panel::partials.field', ['field' => $item])
    @endif
@endforeach
