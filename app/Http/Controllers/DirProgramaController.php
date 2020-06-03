<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivarEstudianteRequest;
use App\Models\Dependencia;
use App\Models\Estudiante;
use App\Models\Genero;
use App\Models\HojaVida;
use App\Models\Municipio;
use App\Models\Persona;
use App\Models\ProcesoGrado;
use App\Models\SolicitudGrado;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\PersonaHelper;
use App\Tools\Variables;
use App\Tools\WSAdmisiones;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirProgramaController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        // rpineda coordinador de programa
        session(['ur' => UsuarioRol::find(22)]);
        \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['coordinador']->nombre);
    }

    public function index()
    {
        return redirect('/direccion/estudiantes');
    }

    public function solicitudes()
    {
        return view('dirprograma.solicitudes');
    }

    public function estudiantes()
    {
        return view('dirprograma.estudiantes');
    }

    public function estudiante()
    {
        return view('dirprograma.estudiante');
    }

    public function activarEstudiante(ActivarEstudianteRequest $request)
    {
        $solicitud = null;

        if ($request->solicitud_id) $solicitud = SolicitudGrado::find($request->solicitud_id);
        else return response('', 400);

        $estados = Variables::estados();
        $estudiante = Estudiante::where('idPrograma', $solicitud->programa_id)
            ->where('codigo', $solicitud->codigo_estudiante)
            ->first();

        if ($estudiante && $estudiante->procesoGrado->estado_programa_id !== $estados['rechazado']->id)
            return response('El estudiante ya se encuentra en el sistema', 400);

        $roles = Variables::roles();
        $pazSalvos = Variables::defaultPazSalvos();
        $tiposEstudiante = Variables::tiposEstudiante();
        $ws = new WSAdmisiones();
        $data = $ws->getInformacionGraduadoByDocumentoIdentidad($solicitud->identificacion_estudiante)[0];
        $data2 = $ws->getInfoEstudianteByCodigo($solicitud->codigo_estudiante)[0];
        $persona = Persona::where('identificacion', $data->numeroDocumento)->first();

        if (!$persona) {
            $ciudadResidencia = Municipio::where('nombre', $data->ciudadResidencia)->first();
            $ciudadOrigen = Municipio::where('nombre', $data->ciudad)->first();
            $tipoDoc = TipoDocumento::where('abrv', $data->tipoDocumento)->first();
            $genero = $data->sexo;

            if ($genero === 'M') $genero = 'MASCULINO';
            else if ($genero === 'F') $genero = 'FEMENINO';
            else $genero = 'OTRO';

            $persona = new Persona();
            $persona->nombres = $data->nombres;
            $persona->apellidos = $data->apellidos;
            $persona->correo = $data->correoPers;
            $persona->correo_institucional = $data->correoInst;
            $persona->celular = $data->telefono1;
            $persona->celular2 = $data->telefono2;
            $persona->identificacion = $data->numeroDocumento;
            $persona->ciudadExpedicion = $data->ciudadCedula;
            $persona->fecha_expedicion = $data->fechaExpDocumento !== '-' ? Carbon::createFromFormat('d/m/Y', $data->fechaExpDocumento) : null;
            $persona->ciudadResidencia = $ciudadResidencia ? $ciudadResidencia->id : null;
            $persona->ciudadOrigen = $ciudadOrigen ? $ciudadOrigen->id : null;
            $persona->fechaNacimiento = Carbon::createFromFormat('d/m/Y', $data->fechaNacimiento);
            $persona->direccion = $data->direccion;
            $persona->estrato = $data->estrato;
            $persona->idGenero = Genero::where('nombre', $genero)->first()->id;
            $persona->tipodoc = $tipoDoc->id;
            $persona->save();
        }

        if (!$persona->hojaVida) {
            $hoja = new HojaVida();
            $hoja->idPersona = $persona->id;
            $hoja->save();
        }

        if (!$persona->usuario) {
            $user = new User();
            $user->idPersona = $persona->id;
            $user->identificacion = explode('@', $persona->correo_institucional)[0];
            $user->activo = true;
            $user->save();
        }

        $persona = Persona::find($persona->id);
        $rol = $persona->usuario->roles()->find($roles['estudiante']->id);

        if (!$rol) $persona->usuario->roles()->attach($roles['estudiante']->id, ['activo' => true]);

        if (!$estudiante) {
            $zonal = $data2->zonal === 'NO DEFINIDO' ? 'SANTA MARTA' : $data2->zonal;
            $municipioZonal = Municipio::where('nombre', $zonal)->first();
            $estudiante = new Estudiante();
            $estudiante->codigo = $data2->codigo;
            $estudiante->idTipo = $tiposEstudiante['egresado']->id;
            $estudiante->idPrograma = $solicitud->programa_id;
            $estudiante->idPersona = $persona->id;
            $estudiante->idZonal = $municipioZonal ? $municipioZonal->id : null;
            $estudiante->save();
        }

        // PAZ Y SALVOS
        $estudiante->pazSalvos()->detach();
        $estudiante->pazSalvos()->attach([
            $pazSalvos['biblioteca']->id,
            $pazSalvos['bienestar']->id,
            $pazSalvos['recursosEducativos']->id,
            $pazSalvos['pago']->id
        ]);

        // DOCUMENTOS
        $documentos = Variables::documentos();

        foreach ($documentos as $doc) {
            if ($doc->id !== $documentos['ecaes']->id) $estudiante->documentos()->detach($doc->id);
        }

        $estudiante->documentos()->attach([
            $documentos['ecaes']->id => ['estado_id' => $estados['sin_cargar']->id],
            $documentos['identificacion']->id => ['estado_id' => $estados['sin_cargar']->id],
            $documentos['paz_salvos']->id => ['estado_id' => $estados['sin_cargar']->id],
            $documentos['ficha']->id => ['estado_id' => $estados['sin_cargar']->id],
            $documentos['titulo_grado']->id => ['estado_id' => $estados['sin_cargar']->id],
            $documentos['ayre']->id => ['estado_id' => $estados['sin_cargar']->id],
        ]);

        $proceso = $estudiante->procesoGrado ?? new ProcesoGrado();
        $proceso->idEstudiante = $estudiante->id;
        $proceso->idFecha = $solicitud->fecha_grado_id;
        $proceso->estado_secretaria_id = $estados['pendiente']->id;
        $proceso->comentario_secretaria = null;
        $proceso->fecha_secretaria = null;
        $proceso->estado_programa_id = $estados['pendiente']->id;
        $proceso->fecha_programa = null;
        $proceso->estatura = null;
        $proceso->talla_camisa = null;
        $proceso->num_acompaniantes = null;
        $proceso->confirmacion_asistencia = null;
        $proceso->save();

        // PersonaHelper::actualizarProgresoFicha($persona);

        if ($solicitud) {
            $solicitud->estado_id = $estados['aprobado']->id;
            $solicitud->fecha_realizacion = Carbon::now();
            $solicitud->save();
        }

        return 'ok';
    }

    public function programasCoordinados()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $usuario = $ur->usuario;

        return $usuario->programas_coordinados;
    }

    public function obtenerEstudiantes(Request $req)
    {
        $this->validate($req, [
            'programa_id' => 'required|exists:dependencias,id',
            'tipo_grado_id' => 'exists:tipos_de_grados,id',
            'fecha_grado_id' => 'exists:fechas_de_grado,id',
            'estado' => 'in:aprobado,no_aprobado,pendiente'
        ]);

        $ur = UsuarioRol::find(session('ur')->id);
        $usuario = $ur->usuario;
        $programa_ids = array_map(fn ($dep) => $dep->id, $usuario->programas_coordinados);
        $programa_id = $req->programa_id;

        if (!in_array($programa_id, $programa_ids)) return response('No permitido', 400);

        $data = [];
        $tipos = Variables::tiposEstudiante();
        $search = $req->search['value'];

        $searchBy = [
            'codigo' => 'estudiantes.codigo',
            'nombres' => 'personas.nombres',
            'apellidos' => 'personas.apellidos',
            'identificacion' => 'personas.identificacion',
            'celular' => 'personas.celular',
            'estado_programa' => 'ep.nombre',
            'estado_secretaria' => 'es.nombre',
        ];

        // DRAW VALUE
        $draw = (int) $req->draw;

        // RECORDS TOTAL
        $estudiantes = Estudiante::where('idTipo', $tipos['egresado']->id)
            ->whereHas('dependenciaModalidad', fn ($dm) => $dm->where('idPrograma', $programa_id))
            ->whereHas('procesoGrado', function ($pg) use ($req) {
                $estados = Variables::estados();

                switch ($req->estado) {
                    case 'aprobado':
                        $pg->where('estado_programa_id', $estados['aprobado']->id);
                        break;

                    case 'pendiente':
                        $pg->where('estado_programa_id', $estados['pendiente']->id);
                        break;

                    case 'no_aprobado':
                        $pg->where('no_aprobado', true);
                        break;
                }

                $pg->whereHas('fechaGrado', function ($fg) use ($req) {
                    $fg->where('estado', true);

                    if ($req->tipo_grado_id) $fg->where('tipo_grado', $req->tipo_grado_id);
                    if ($req->fecha_grado_id) $fg->where('id', $req->fecha_grado_id);
                });
            });

        $recordsTotal = $estudiantes->count();

        // RECORDS FILTERED
        $estudiantes = $estudiantes->join('personas', 'personas.id', '=', 'estudiantes.idPersona')
            ->join('proceso_grado', 'proceso_grado.idEstudiante', '=', 'estudiantes.id')
            ->join('estado as ep', 'ep.id', '=', 'proceso_grado.estado_programa_id')
            ->join('estado as es', 'es.id', '=', 'proceso_grado.estado_secretaria_id')
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
                'DT_RowData' => [
                    'id' => $estudiante->id
                ],
                'foto' => null,
                'codigo' => $estudiante->codigo,
                'nombres' => $persona->nombres,
                'apellidos' => $persona->apellidos,
                'identificacion' => $persona->identificacion,
                'celular' => $persona->celular,
                'estado_programa' => $pg->estadoPrograma->nombre,
                'estado_secretaria' => $pg->estadoSecretaria->nombre,
                'estado_secretaria' => $pg->estadoSecretaria->nombre,
                'acciones' => ''
            ]);
        }

        return compact('data', 'draw', 'recordsTotal', 'recordsFiltered');
    }

    public function procesoGrado($estudiante_id)
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->estudiantes_coordinados->find($estudiante_id);

        if (!$estudiante) return response('No permitido', 400);

        $paz_salvos = [];
        $info = [
            'nombre' => $estudiante->persona->nombre,
            'codigo' => $estudiante->codigo,
            'programa' => $estudiante->estudio->programa->nombre,
            'celular' => $estudiante->persona->celular,
            'correo' => $estudiante->persona->correo,
            'foto' => null
        ];

        $pg = $estudiante->procesoGrado;
        $proceso = [
            [
                'proceso' => 'Registro de la encuesta (Momento de Grado)',
                'responsable' => 'Estudiante',
                'estado' => $pg->estado_encuesta ? 'APROBADO' : 'PENDIENTE'
            ],
            [
                'proceso' => 'Ficha de egresado',
                'responsable' => 'Estudiante',
                'estado' => $pg->estado_ficha ? 'APROBADO' : 'PENDIENTE'
            ],
            [
                'proceso' => 'Carga de los documentos de grado',
                'responsable' => 'Estudiante',
                'estado' => $estudiante->estado_documentos
            ],
            [
                'proceso' => 'Confirmación de asistencia a ceremonia de grado',
                'responsable' => 'Estudiante',
                'estado' => $pg->confirmacion_asistencia ? 'APROBADO' : 'PENDIENTE'
            ],
            [
                'proceso' => 'Aprobación del proceso de grado',
                'responsable' => 'Dirección de programa',
                'estado' => $pg->estadoPrograma->nombre
            ]
        ];

        foreach ($estudiante->estudiantePazSalvo as $eps) {
            $ps = $eps->pazSalvo;

            array_push($paz_salvos, [
                'nombre' => $ps->nombre,
                'dependencia' => $ps->dependencia ? $ps->dependencia->nombre : null,
                'comentario' => $eps->comentario,
                'estado' => $eps->paz_salvo ? 'APROBADO' : 'PENDIENTE',
                'fecha' => $eps->fecha,
            ]);
        }

        return compact('info', 'proceso', 'paz_salvos');
    }

    public function datosEstudiante($estudiante_id)
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->estudiantes_coordinados->find($estudiante_id);

        if (!$estudiante) return response('No permitido', 400);

        $persona = $estudiante->persona;
        return [
            'id' => $estudiante->id,
            'foto' => null,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'tipo_documento' => $persona->tipoDocumento->abrv,
            'documento' => $persona->identificacion,
            'municipio_expedicion' => $persona->ciudadExpedicion,
            'lugar_nacimiento' => $persona->lugar_nacimiento,
            'fecha_nacimiento' => $persona->fechaNacimiento,
            'correo' => $persona->correo,
            'celular' => $persona->celular,
            'programa' => $estudiante->estudio->programa->nombre,
            'codigo' => $estudiante->codigo,
        ];
    }

    public function documentosEstudiante($estudiante_id)
    {
        $documentos = Variables::documentos();
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->estudiantes_coordinados->find($estudiante_id);

        $res = [
            'can_aprobar' => $estudiante->can_aprobar,
            'documentos' => []
        ];

        foreach ($documentos as $doc) {
            $ed = $estudiante->estudianteDocumento()->where('idDocumento', $doc->id)->first();

            if ($ed) array_push($res['documentos'], [
                'id' => $ed->id,
                'nombre' => $ed->documento->nombre,
                'estado' => $ed->estado->nombre,
                'can_generar' => $ed->can_generar,
                'can_show' => $ed->can_show,
                'can_aprobar' => $ed->can_aprobar,
                'can_rechazar' => $ed->can_rechazar,
                'can_cargar' => $ed->can_cargar_direccion,
            ]);
        }

        return $res;
    }
}
