<?php

namespace App\Http\Controllers;

use App\Models\ActividadEconomica;
use App\Models\AreaDesempeno;
use App\Models\Concejo;
use App\Models\Departamento;
use App\Models\Dependencia;
use App\Models\DependenciaModalidad;
use App\Models\Discapacidad;
use App\Models\DistincionEstudiante;
use App\Models\Documento;
use Illuminate\Http\Request;

use App\Models\Duracion;
use App\Models\EstadoCivil;
use App\Models\FechaGrado;
use App\Models\Genero;
use App\Models\Idioma;
use App\Models\Jornada;
use App\Models\ModalidadEstudio;
use App\Models\Municipio;
use App\Models\NivelCargo;
use App\Models\NivelEstudio;
use App\Models\NivelIdioma;
use App\Models\Pais;
use App\Models\PazSalvo;
use App\Models\Salario;
use App\Models\SectorEconomico;
use App\Models\SectorEmpresa;
use App\Models\TipoDocumento;
use App\Models\TipoGrado;
use App\Models\TipoVinculacion;
use App\Tools\Variables;

class RecursosController extends Controller
{
    public function __construct()
    {
    }

    public function duracionesLaborales()
    {
        return Duracion::all();
    }

    public function idiomas()
    {
        return Idioma::all();
    }

    public function nivelesCargo()
    {
        return NivelCargo::all();
    }

    public function nivelesIdioma()
    {
        return NivelIdioma::all();
    }

    public function paises()
    {
        return Pais::orderBy('nombre')->get();
    }

    public function departamentos(Request $request)
    {
        $this->validate($request, [
            'pais' => 'exists:paises,id'
        ]);

        $departamentos = new Departamento();
        if ($request->pais) $departamentos = $departamentos->where('idPais', $request->pais);

        return $departamentos->orderBy('nombre')->get();
    }

    public function municipios(Request $request)
    {
        $this->validate($request, [
            'departamento' => 'exists:departamentos,id'
        ]);

        $municipios = new Municipio();
        if ($request->departamento) $municipios = $municipios->where('idDepartamento', $request->departamento);

        return $municipios->orderBy('nombre')->get();
    }

    public function salarios()
    {
        return Salario::all();
    }

    public function tiposVinculacion()
    {
        return TipoVinculacion::all();
    }

    public function generos()
    {
        return Genero::all();
    }

    public function tiposDocumento()
    {
        return TipoDocumento::all();
    }

    public function estadosCiviles()
    {
        return EstadoCivil::all();
    }

    public function discapacidades()
    {
        return Discapacidad::all();
    }

    public function consejos()
    {
        return Concejo::all();
    }

    public function roles()
    {
        $res = [];
        $roles = Variables::roles();

        $blacklistElegir = [$roles['estudiante']->id];

        foreach ($roles as $rol) {
            array_push($res, [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
                'canElegirProgramas' => $rol->id === $roles['coordinador']->id,
                'canElegirDependencias' => $rol->id === $roles['dependencia']->id,
                'canElegir' => !in_array($rol->id, $blacklistElegir),
            ]);
        }

        return $res;
    }

    public function programas(Request $request)
    {
        $dms = new DependenciaModalidad();
        if ($request->facultad_id) $dms = $dms->where('idFacultad', $request->facultad_id);

        $dms = $dms->select('idPrograma')->distinct()->get();
        $programas = $dms->map(fn ($dm) => $dm->programa()->select('id', 'nombre')->first())
            ->sortBy('nombre')
            ->values()
            ->all();

        return $programas;
    }

    public function dependencias()
    {
        $tipos = Variables::tiposDependencia();
        $dependencias = Dependencia::where('idTipo', $tipos['administrativa']->id)->select('id', 'nombre')->get();

        return $dependencias;
    }

    public function nivelesEstudio()
    {
        return NivelEstudio::all();
    }

    public function tiposGrado()
    {
        $tipos = Variables::tiposGrado();
        $res = [];

        foreach ($tipos as $key => $tipo) {
            if ($key !== 'no_reporta') array_push($res, $tipo);
        }

        return $res;
    }

    public function fechasGrado(Request $request)
    {
        $this->validate($request, [
            'activa' => 'boolean',
            'tipo_grado_id' => 'exists:tipos_de_grados,id',
            'fecha_min' => 'date',
            'fecha_max' => 'date'
        ]);

        $query = new FechaGrado();

        if ($request->activa !== null) $query = $query->where('estado', $request->activa);
        if ($request->tipo_grado_id !== null) $query = $query->where('tipo_grado', $request->tipo_grado_id);
        if ($request->fecha_min !== null) $query = $query->where('fecha_grado', '>=', $request->fecha_min);
        if ($request->fecha_max !== null) $query = $query->where('fecha_grado', '<=', $request->fecha_max);

        return $query->orderBy('fecha_grado', 'desc')->get()->map(fn ($fec) => [
            'id' => $fec->id,
            'nombre' => $fec->nombre,
            'fecha' => $fec->fecha_grado,
            'tipo' => $fec->tipoGrado ? $fec->tipoGrado->nombre : null,
        ])->all();
    }

    public function sectoresEmpresa()
    {
        return SectorEmpresa::all();
    }

    public function sectoresEconomicos()
    {
        return SectorEconomico::all();
    }

    public function actividadesEconomicas()
    {
        return ActividadEconomica::orderBy('nombre')->get();
    }

    public function areasDesempeno()
    {
        return AreaDesempeno::all();
    }

    public function pazSalvos()
    {
        return PazSalvo::select('id', 'nombre')->get();
    }

    public function facultades(Request $request)
    {
        $dms = new DependenciaModalidad();
        if ($request->programa_id) $dms = $dms->where('idPrograma', $request->programa_id);

        $dms = $dms->select('idFacultad')->distinct()->get();
        $res = $dms->map(fn ($dm) => $dm->facultad()->select('id', 'nombre')->first())
            ->sortBy('nombre')
            ->values()
            ->all();

        return $res;
    }

    public function modalidadesEstudio(Request $request)
    {
        $dms = new DependenciaModalidad();
        if ($request->programa_id) $dms = $dms->where('idPrograma', $request->programa_id);

        $dms = $dms->select('idModalidad')->distinct()->get();
        $res = $dms->map(fn ($dm) => $dm->modalidad)
            ->sortBy('nombre')
            ->values()
            ->all();

        return $res;
    }

    public function jornadas(Request $request)
    {
        $dms = new DependenciaModalidad();
        if ($request->programa_id) $dms = $dms->where('idPrograma', $request->programa_id);

        $dms = $dms->select('idJornada')->distinct()->get();
        $res = $dms->map(fn ($dm) => $dm->jornada)
            ->sortBy('nombre')
            ->values()
            ->all();

        return $res;
    }

    public function documentos()
    {
        return Documento::all();
    }

    public function distincionesEstudiante()
    {
        return DistincionEstudiante::all();
    }
}
