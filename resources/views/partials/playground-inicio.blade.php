<div class="panel-playground-inicio">
    <p class="panel-playground-inicio-lead">{{ __('panel::panel.documentation.welcome_lead') }}</p>

    <ol class="panel-playground-pasos">
        <li class="panel-playground-paso">
            <span class="panel-playground-paso-num">1</span>
            <div>
                <p class="panel-heading text-xs font-semibold">{{ __('panel::panel.documentation.step_1_title') }}</p>
                <p class="panel-muted text-xs">{{ __('panel::panel.documentation.step_1_desc') }}</p>
            </div>
        </li>
        <li class="panel-playground-paso">
            <span class="panel-playground-paso-num">2</span>
            <div>
                <p class="panel-heading text-xs font-semibold">{{ __('panel::panel.documentation.step_2_title') }}</p>
                <p class="panel-muted text-xs">{{ __('panel::panel.documentation.step_2_desc') }}</p>
            </div>
        </li>
        <li class="panel-playground-paso">
            <span class="panel-playground-paso-num">3</span>
            <div>
                <p class="panel-heading text-xs font-semibold">{{ __('panel::panel.documentation.step_3_title') }}</p>
                <p class="panel-muted text-xs">{{ __('panel::panel.documentation.step_3_desc') }}</p>
            </div>
        </li>
    </ol>

    <div class="panel-playground-inicio-acciones">
        <button type="button" wire:click="seleccionarSeccion('apariencia')" class="panel-btn panel-btn-primary w-full text-sm">
            {{ __('panel::panel.documentation.start_customize') }}
        </button>
        <button type="button" wire:click="seleccionarSeccion('graficos')" class="panel-btn panel-btn-ghost w-full text-sm">
            {{ __('panel::panel.documentation.go_charts') }}
        </button>
        <button type="button" wire:click="seleccionarSeccion('auth')" class="panel-btn panel-btn-ghost w-full text-sm">
            {{ __('panel::panel.documentation.go_auth') }}
        </button>
        <button type="button" wire:click="seleccionarSeccion('codigo')" class="panel-btn panel-btn-ghost w-full text-sm">
            {{ __('panel::panel.documentation.go_export') }}
        </button>
    </div>
</div>
