<?php

namespace App\Http\Controllers;

use App\Exports\SNIESExport;
use App\Http\Requests\FiltroEstudiantesRequest;
use App\Models\Estudiante;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

class SecretariaGeneralController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        // julianpitreap sec general
        session(['ur' => UsuarioRol::find(20029)]);
        \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['secretariaGeneral']->nombre, ['except' => [
            'obtenerEstudiantes',
            'generarSnies'
        ]]);
        $this->middleware(
            'rol:' . $roles['secretariaGeneral']->nombre . '|' . $roles['administrador']->nombre,
            ['only' => [
                'obtenerEstudiantes',
            ]]
        );
        $this->middleware(
            'rol:' . $roles['secretariaGeneral']->nombre . '|' . $roles['coordinador']->nombre,
            ['only' => [
                'obtenerEstudiantes',
                'generarSnies'
            ]]
        );
    }

    public function index()
    {
        return redirect('/secgeneral/estudiantes');
    }

    public function estudiantes()
    {
        return view('secgeneral.estudiantes');
    }

    public function getEstudiantes($params)
    {
        $tipos = Variables::tiposEstudiante();
        $estudiantes = Estudiante::where('estudiantes.idTipo', $tipos['egresado']->id)
            ->whereHas('dependenciaModalidad', function ($dm) use ($params) {
                if ($params->programa_id) $dm->where('idPrograma', $params->programa_id);
            })
            ->whereHas('procesoGrado', function ($pg) use ($params) {
                $estados = Variables::estados();

                switch ($params->estado) {
                    case 'aprobado':
                        $pg->where('estado_secretaria_id', $estados['aprobado']->id)->where('no_aprobado', false);
                        break;

                    case 'pendiente':
                        $pg->where('estado_secretaria_id', $estados['pendiente']->id)->where('no_aprobado', false);
                        break;

                    case 'no_aprobado':
                        $pg->where('no_aprobado', true);
                        break;
                }

                $pg->whereHas('fechaGrado', function ($fg) use ($params) {
                    if ($params->tipo_grado_id) $fg->where('tipo_grado', $params->tipo_grado_id);
                    if ($params->fecha_grado_id) $fg->where('id', $params->fecha_grado_id);
                    else $fg->where('estado', true);
                });
            });

        return $estudiantes;
    }

    public function obtenerEstudiantes(FiltroEstudiantesRequest $req)
    {
        $data = [];
        $search = $req->search['value'];
        $searchBy = [
            'codigo' => 'estudiantes.codigo',
            'nombres' => 'personas.nombres',
            'apellidos' => 'personas.apellidos',
            'programa' => 'prg.nombre',
            'fecha_grado' => 'fechas_de_grado.nombre',
            'estado_programa' => 'ep.nombre',
            'estado_secretaria' => 'es.nombre',
        ];

        // DRAW VALUE
        $draw = (int) $req->draw;

        // RECORDS TOTAL
        $estudiantes = $this->getEstudiantes($req);
        $recordsTotal = $estudiantes->count();

        // RECORDS FILTERED
        $estudiantes = $estudiantes->join('personas', 'personas.id', '=', 'estudiantes.idPersona')
            ->join('proceso_grado', 'proceso_grado.idEstudiante', '=', 'estudiantes.id')
            ->join('estado as ep', 'ep.id', '=', 'proceso_grado.estado_programa_id')
            ->join('estado as es', 'es.id', '=', 'proceso_grado.estado_secretaria_id')
            ->join('fechas_de_grado', 'fechas_de_grado.id', '=', 'proceso_grado.idFecha')
            ->join('dependencias_modalidades', 'dependencias_modalidades.id', '=', 'estudiantes.idPrograma')
            ->join('dependencias as prg', 'prg.id', '=', 'dependencias_modalidades.idPrograma')
            ->where(function ($query) use ($search, $searchBy) {
                foreach (array_values($searchBy) as $key => $prop) {
                    if ($key === 0) $query->where($prop, 'like', "%$search%");
                    else $query->orWhere($prop, 'like', "%$search%");
                }
            });

        $recordsFiltered = $estudiantes->count();

        // ORDER
        $order = $req->order[0];
        $orderColumn = $req->columns[$order['column']]['data'];
        $filter = array_filter($searchBy, fn ($key) => $key === $orderColumn, ARRAY_FILTER_USE_KEY);
        $values = array_values($filter);

        if (count($values) > 0) {
            $orderProp = $values[0];
            $orderDir = $order['dir'];
            $estudiantes = $estudiantes->orderBy($orderProp, $orderDir);
        }

        // SELECT AND GET RESULTS
        $estudiantes = $estudiantes
            ->select('estudiantes.id')
            ->skip($req->start)
            ->take($req->length)
            ->get();

        // CALCULATE DATA
        foreach ($estudiantes as $est) {
            $estudiante = Estudiante::find($est->id);
            $persona = $estudiante->persona;
            $pg = $estudiante->procesoGrado;
            $no_aprobado = $estudiante->estado === Variables::$estadoNoAprobado;

            array_push($data, [
                'DT_RowData' => [
                    'id' => $estudiante->id
                ],
                'foto' => null,
                'codigo' => $estudiante->codigo,
                'nombres' => $persona->nombres,
                'apellidos' => $persona->apellidos,
                'fecha_grado' => $pg->fechaGrado->fecha_grado,
                'programa' => $estudiante->estudio->programa->nombre,
                'estado_programa' => $no_aprobado ? Variables::$estadoNoAprobado : $pg->estadoPrograma->nombre,
                'estado_secretaria' => $no_aprobado ? Variables::$estadoNoAprobado : $pg->estadoSecretaria->nombre,
                'acciones' => ''
            ]);
        }

        return compact('data', 'draw', 'recordsTotal', 'recordsFiltered');
    }

    public function vistaAprobados()
    {
        return view('secgeneral.estudiantes', ['backup' => true]);
    }

    public function generarSnies(FiltroEstudiantesRequest $request)
    {
        $estudiantes = $this->getEstudiantes($request)->get();
        $export = new SNIESExport($estudiantes);

        return $export->download('estudiantes.xlsx');
    }
}
