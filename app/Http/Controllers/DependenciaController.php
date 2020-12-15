<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Tools\MailHelper;
use App\Tools\Variables;
use Illuminate\Http\Request;

class DependenciaController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['dependencia']->nombre);
    }

    private function getEstudiantes()
    {
        $estudiantes = Estudiante::egresados()
            ->whereHas('estudiantePazSalvo.pazSalvo', function ($ps) {
                $ps->where('idDependencia', session('dependencia_id'));
            });

        return $estudiantes;
    }

    public function index()
    {
        return redirect('/dependencia/administrar-estudiantes');
    }

    public function administrarEstudiantes()
    {
        return view('dependencia.administrar_estudiantes');
    }

    public function filtrarEstudiantes($params)
    {
        $estudiantes = Estudiante::egresados()
            ->whereHas('estudiantePazSalvo', function ($eps) use ($params) {
                $estado = $params->estado === 'aprobado';
                $eps->where('paz_salvo', $estado)->whereHas('pazSalvo', function ($ps) {
                    $ps->where('idDependencia', session('dependencia_id'));
                });
            })
            ->whereHas('estudio.programa', function ($prg) use ($params) {
                if ($params->programa_id) $prg->where('id', $params->programa_id);
            })
            ->whereHas('procesoGrado.fechaGrado', function ($fg) use ($params) {
                if ($params->fecha_grado_inicio) $fg->where('fecha_grado', '>=', $params->fecha_grado_inicio);
                if ($params->fecha_grado_final) $fg->where('fecha_grado', '<=', $params->fecha_grado_final);
            });

        return $estudiantes;
    }

    public function obtenerEstudiantes(Request $req)
    {
        $data = [];
        $search = $req->search['value'];
        $searchBy = [
            'codigo' => 'estudiantes.codigo',
            'nombres' => 'personas.nombres',
            'apellidos' => 'personas.apellidos',
            'programa' => 'prg.nombre',
            'fecha_grado' => 'fechas_de_grado.nombre',
        ];

        // DRAW VALUE
        $draw = (int) $req->draw;

        // RECORDS TOTAL
        $estudiantes = $this->filtrarEstudiantes($req);
        $recordsTotal = $estudiantes->count();

        // RECORDS FILTERED
        $estudiantes = $estudiantes->join('personas', 'personas.id', '=', 'estudiantes.idPersona')
            ->join('proceso_grado', 'proceso_grado.idEstudiante', '=', 'estudiantes.id')
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

            array_push($data, [
                'nombre' => $persona->nombre,
                'programa' => $estudiante->estudio->programa->nombre,
                'codigo' => $estudiante->codigo,
                'foto' => $estudiante->foto,
                'fecha_grado' => $pg->fechaGrado->fecha_grado,
                'DT_RowData' => [
                    'id' => $estudiante->id
                ],
                'acciones' => ''
            ]);
        }

        return compact('data', 'draw', 'recordsTotal', 'recordsFiltered');
    }

    public function cambiarEstado(Request $req)
    {
        $estudiante = $this->getEstudiantes()->find($req->estudiante_id);
        $eps = $estudiante->estudiantePazSalvo()
            ->whereHas('pazSalvo', function ($ps) {
                $ps->where('idDependencia', session('dependencia_id'));
            })->first();

        if (!$eps) return response('No se encuentra paz y salvo del estudiante', 400);

        $estadoActual = $eps->paz_salvo;

        if ($estadoActual === true) {
            $persona = $estudiante->persona;
            $content = 'Su paz y salvo de ' . $eps->pazSalvo->nombre . ' ha sido rechazado debido a ' . $req->motivo;
            MailHelper::enviarCorreo($content, [$persona->correo_institucional], (object) [
                'name' => $persona->nombre,
                'subject' => 'PAZ Y SALVO RECHAZADO - SAEG UNIMAGDALENA',
            ]);
        }

        $eps->paz_salvo = !$estadoActual;
        $eps->save();

        return 'ok';
    }
}
