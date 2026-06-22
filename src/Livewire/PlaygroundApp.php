<?php



declare(strict_types=1);



namespace MyLaravelTools\Panel\Livewire;



use MyLaravelTools\Panel\Support\PanelDocumentacion;

use MyLaravelTools\Panel\Support\PanelLayout;

use MyLaravelTools\Panel\Support\PanelPlayground;

use MyLaravelTools\Panel\Support\PanelPlaygroundGraficos;

use MyLaravelTools\Panel\Support\PanelPlaygroundVista;

use Livewire\Attributes\Layout;

use Livewire\Component;



#[Layout('panel::layouts.playground')]

final class PlaygroundApp extends Component

{

    /** @var array<string, mixed> */

    public array $valores = [];



    /** @var array<string, mixed> */

    public array $graficos = [];



    public string $seccionActiva = 'inicio';



    public string $seccionTecnica = 'layout';



    public bool $mostrarControles = false;



    public int $revisionEscenario = 0;



    public int $revisionTema = 0;



    public int $revisionGraficos = 0;



    public ?string $zonaResaltada = null;



    public function mount(): void

    {

        abort_unless(config('panel.documentation.enabled', true), 404);



        PanelPlayground::aplicarDesdeSesion();

        $this->valores = PanelPlayground::valores();

        $this->graficos = PanelPlaygroundGraficos::valores();

    }



    public function boot(): void

    {

        PanelPlayground::aplicarDesdeSesion();

    }



    public function updated(string $property): void

    {

        if (str_starts_with($property, 'graficos.')) {

            $clave = substr($property, strlen('graficos.'));

            PanelPlaygroundGraficos::guardar($clave, data_get($this->graficos, $clave));

            $this->revisionGraficos++;

            $this->zonaResaltada = 'graficos';

            $this->dispatch('playground-resaltar-zona', zona: 'graficos');

            $this->dispatch('playground-graficos-actualizado');



            return;

        }



        if (! str_starts_with($property, 'valores.')) {

            return;

        }



        $clave = substr($property, strlen('valores.'));

        PanelPlayground::guardar($clave, data_get($this->valores, $clave));

        PanelPlayground::aplicarDesdeSesion();

        $this->zonaResaltada = PanelPlaygroundVista::zonaPorClave($clave);

        $this->dispatch('playground-resaltar-zona', zona: $this->zonaResaltada);



        if ($clave === 'theme.default') {

            $this->dispatch(

                'playground-tema-actualizado',

                tema: (string) config('panel.theme.default', 'dark'),

            );

        }



        if (self::requiereActualizacionTema($clave)) {

            $this->dispatch('playground-mostrar-carga');

            if ($clave === 'theme.preset') {

                $this->valores = PanelPlayground::valores();

            }



            $this->revisionTema++;

            $this->dispatch('playground-tema-aplicado');

        }



        if (self::requiereReinicioEscenario($clave)) {

            $this->revisionEscenario++;

        }

    }



    public function seleccionarTipoGrafico(string $tipo): void

    {

        if (! in_array($tipo, PanelPlaygroundGraficos::TIPOS, true)) {

            return;

        }



        $this->graficos['tipo_activo'] = $tipo;

        PanelPlaygroundGraficos::guardar('tipo_activo', $tipo);

        $this->revisionGraficos++;

        $this->zonaResaltada = 'graficos';

        $this->dispatch('playground-resaltar-zona', zona: 'graficos');

        $this->dispatch('playground-graficos-actualizado');

    }



    public function reiniciar(): void

    {

        PanelPlayground::reiniciar();

        PanelPlaygroundGraficos::reiniciar();

        PanelPlayground::aplicarDesdeSesion();

        $this->valores = PanelPlayground::valores();

        $this->graficos = PanelPlaygroundGraficos::valores();

        $this->revisionEscenario++;

        $this->revisionTema++;

        $this->revisionGraficos++;

        $this->zonaResaltada = null;

        $this->dispatch(

            'playground-reiniciar-tema',

            tema: (string) config('panel.theme.default', 'dark'),

        );

        $this->dispatch('playground-tema-aplicado');

        $this->dispatch('playground-graficos-actualizado');

    }



    private static function requiereActualizacionTema(string $clave): bool

    {

        return str_starts_with($clave, 'theme.');

    }



    private static function requiereReinicioEscenario(string $clave): bool

    {

        return str_starts_with($clave, 'layout.') || str_starts_with($clave, 'brand.');

    }



    public function enfocarZona(string $zona): void

    {

        $this->zonaResaltada = $zona;

    }



    public function limpiarZonaResaltada(): void

    {

        $this->zonaResaltada = null;

    }



    public function seleccionarSeccionTecnica(string $seccion): void

    {

        $this->seccionTecnica = $seccion;

    }



    public function seleccionarSeccion(string $seccion): void

    {

        $this->seccionActiva = $seccion;



        if ($seccion === 'graficos') {

            $this->zonaResaltada = 'graficos';

            $this->dispatch('playground-resaltar-zona', zona: 'graficos');

        }

    }



    public function alternarControles(): void

    {

        $this->mostrarControles = ! $this->mostrarControles;

    }



    public function render(): mixed

    {

        PanelPlayground::aplicarDesdeSesion();



        return view('panel::livewire.playground-app', [

            'grupos' => PanelDocumentacion::gruposUsuario(),

            'secciones' => PanelDocumentacion::secciones(),

            'clasesTabla' => PanelLayout::clasesTabla(),

            'marca' => (string) config('panel.brand.name', 'Panel'),

            'modo' => PanelLayout::modo(),

            'fragmentoConfig' => PanelPlayground::exportarFragmento(),

            'archivoConfig' => PanelPlayground::exportarArchivo(),

            'cambios' => PanelPlayground::listarCambios(),

            'tieneCambios' => PanelPlayground::tieneSobreescrituras(),

            'revisionTema' => $this->revisionTema,

            'revisionGraficos' => $this->revisionGraficos,

            'zonasModificadas' => PanelPlaygroundVista::zonasModificadas(),

            'zonaResaltada' => $this->zonaResaltada,

            'widgetsGraficos' => PanelPlaygroundGraficos::todosLosWidgets(),

            'codigoGrafico' => PanelPlaygroundGraficos::exportarCodigo(),

        ])->title(__('panel::panel.documentation.playground_title'));

    }

}


