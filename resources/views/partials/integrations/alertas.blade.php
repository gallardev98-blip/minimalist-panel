@if (\MyLaravelTools\Panel\Support\PanelIntegrations::alertasEnabled())
    {!! view('alertas::components.contenedor')->render() !!}
@endif
