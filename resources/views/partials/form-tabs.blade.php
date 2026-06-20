@php
    use MyLaravelTools\Panel\Support\FormSchema;

    $tabs = FormSchema::tabs($formSchema);
    $firstTabId = $tabs[0]->getId();
@endphp

<div x-data="{ activeTab: @js($firstTabId) }" class="panel-form-tabs">
    <div class="panel-form-tabs-nav" role="tablist" aria-label="{{ __('panel::panel.form_tabs') }}">
        @foreach ($tabs as $tab)
            <button
                type="button"
                role="tab"
                id="panel-tab-{{ $tab->getId() }}"
                aria-controls="panel-tabpanel-{{ $tab->getId() }}"
                :aria-selected="activeTab === @js($tab->getId())"
                @click="activeTab = @js($tab->getId())"
                class="panel-form-tab"
                :class="{ 'panel-form-tab--active': activeTab === @js($tab->getId()) }"
            >
                {{ $tab->getLabel() }}
            </button>
        @endforeach
    </div>

    @foreach ($tabs as $tab)
        <div
            id="panel-tabpanel-{{ $tab->getId() }}"
            role="tabpanel"
            aria-labelledby="panel-tab-{{ $tab->getId() }}"
            x-show="activeTab === @js($tab->getId())"
            x-cloak
            class="panel-form-tab-panel"
        >
            @include('panel::partials.form-schema-items', ['formSchema' => $tab->getSchema()])
        </div>
    @endforeach
</div>
