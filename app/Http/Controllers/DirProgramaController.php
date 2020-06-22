<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivarEstudianteRequest;
use App\Http\Requests\AprobarEstudianteRequest;
use App\Models\Dependencia;
use App\Models\Estudiante;
use App\Models\EstudianteDocumento;
use App\Models\Genero;
use App\Models\HojaVida;
use App\Models\Municipio;
use App\Models\Persona;
use App\Models\ProcesoGrado;
use App\Models\SolicitudGrado;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\DocumentoHelper;
use App\Tools\Variables;
use App\Tools\WSAdmisiones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirProgramaController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        // rpineda coordinador de programa
        session(['ur' => UsuarioRol::find(22)]);
        // julianpitreap sec general
        // session(['ur' => UsuarioRol::find(20029)]);
        \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['coordinador']->nombre, ['except' => [
            'getInfoAdicionalEstudiante',
            'documentosEstudiante',
            'rechazarDocumento',
            'noAprobarEstudiante',
            'aprobarEstudiante',
            'datosEstudiante'
        ]]);
        $this->middleware('rol:' . $roles['coordinador']->nombre . '|' . $roles['secretariaGeneral']->nombre, ['only' => [
            'getInfoAdicionalEstudiante',
            'documentosEstudiante',
            'rechazarDocumento',
            'noAprobarEstudiante',
            'aprobarEstudiante',
            'datosEstudiante'
        ]]);
    }

    private function getEstudiante($estudiante_id)
    {
        $estudiante = null;
        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);

        switch ($ur->rol_id) {
            case $roles['coordinador']->id:
                $estudiante = $ur->usuario->estudiantes_coordinados->find($estudiante_id);
                break;

            case $roles['secretariaGeneral']->id:
                $estudiante = Estudiante::find($estudiante_id);
                break;
        }

        return $estudiante;
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

    private function fetchPazSalvos($estudiante_id)
    {
        # code...
    }

    private function fetchDocumentoIdentidad($estudiante_id)
    {
        # code...
    }

    public function moverDocumentos($eds, $old_folder)
    {
        $ed_ecaes_old = null;
        $same_folder = false;
        $documentos = Variables::documentos();
        $estados = Variables::estados();
        $estudiante = Estudiante::find($eds[0]->idEstudiante);
        $ed_ecaes = $estudiante->estudianteDocumento->where('idDocumento', $documentos['ecaes']->id)->first();

        foreach ($eds as $ed) {
            if ($ed->idDocumento === $documentos['ecaes']->id) $ed_ecaes_old = $ed;
        }

        // dd($ed_ecaes_old,  $old_folder, $ed_ecaes);

        if ($ed_ecaes_old && $ed_ecaes_old->estado_id === $estados['aprobado']->id) {
            if ($ed_ecaes_old->url_documento !== $ed_ecaes->path) {
                Storage::move($ed_ecaes_old->url_documento, $ed_ecaes->path);
                Storage::delete($ed_ecaes_old->url_documento);
            } else $same_folder = true;

            $ed_ecaes->url_documento = $ed_ecaes->path;
            $ed_ecaes->estado_id = $estados['pendiente']->id;
            $ed_ecaes->save();
        }

        $files = Storage::files($old_folder);
        foreach ($files as $file) {
            if (!($same_folder && stristr($file, $ed_ecaes->filename))) Storage::delete($file);
        }
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

        if ($estudiante && !$estudiante->procesoGrado->no_aprobado)
            return response('El estudiante ya se encuentra en el sistema', 400);

        $roles = Variables::roles();
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
        $estudiante->pazSalvos()->attach($estudiante->paz_salvos_iniciales);

        // DOCUMENTOS
        $eds = $estudiante->estudianteDocumento;
        $old_folder = $eds[0]->folder;

        $estudiante->documentos()->detach();
        $estudiante->documentos()->attach($estudiante->documentos_iniciales);

        $proceso = $estudiante->procesoGrado ?? new ProcesoGrado();
        $proceso->idEstudiante = $estudiante->id;
        $proceso->idFecha = $solicitud->fecha_grado_id;
        $proceso->estado_secretaria_id = $estados['pendiente']->id;
        $proceso->comentario_secretaria = null;
        $proceso->fecha_secretaria = null;
        $proceso->estado_programa_id = $estados['pendiente']->id;
        $proceso->fecha_programa = null;
        $proceso->estado_ficha = 0;
        $proceso->fecha_ficha = null;
        $proceso->estado_encuesta = 0;
        $proceso->fecha_encuesta = null;
        $proceso->estatura = null;
        $proceso->talla_camisa = null;
        $proceso->num_acompaniantes = null;
        $proceso->confirmacion_asistencia = null;
        $proceso->titulo_grado = $data->tituloProfesional;
        $proceso->modalidad_grado = $data->opcionGrado;
        $proceso->titulo_memoria_grado = $data->descripcionOpcionGrado;
        $proceso->nota = $data->notaOpcionGrado;
        $proceso->no_aprobado = false;
        // $proceso->motivo_no_aprobado = null;
        $proceso->save();

        $this->moverDocumentos($eds, $old_folder);
        $this->fetchPazSalvos($estudiante->id);
        $this->fetchDocumentoIdentidad($estudiante->id);

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
                'estado' => $estudiante->estado,
                'estado_programa' => $no_aprobado ? Variables::$estadoNoAprobado : $pg->estadoPrograma->nombre,
                'estado_secretaria' => $no_aprobado ? Variables::$estadoNoAprobado : $pg->estadoSecretaria->nombre,
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
            // [
            //     'proceso' => 'Aprobación del proceso de grado',
            //     'responsable' => 'Dirección de programa',
            //     'estado' => $pg->estadoPrograma->nombre
            // ]
        ];

        foreach ($estudiante->estudiantePazSalvo as $eps) {
            $ps = $eps->pazSalvo;

            array_push($paz_salvos, [
                'nombre' => strtolower($ps->nombre),
                'dependencia' => $ps->dependencia ? strtolower($ps->dependencia->nombre) : null,
                'comentario' => $eps->comentario,
                'estado' => $eps->paz_salvo ? 'APROBADO' : 'PENDIENTE',
                'fecha' => $eps->fecha,
            ]);
        }

        return compact('info', 'proceso', 'paz_salvos');
    }

    public function datosEstudiante($estudiante_id)
    {
        $estudiante = $this->getEstudiante($estudiante_id);
        if (!$estudiante) return response('No permitido', 400);

        $persona = $estudiante->persona;
        $pg = $estudiante->procesoGrado;

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
            'fecha_expedicion' => $persona->fecha_expedicion,
            'correo' => $persona->correo,
            'celular' => $persona->celular,
            'programa' => $estudiante->estudio->programa->nombre,
            'codigo' => $estudiante->codigo,
            'titulo_grado' => $pg->titulo_grado,
            'modalidad_grado' => $pg->modalidad_grado,
            'descripcion_opcion_grado' => $pg->titulo_memoria_grado,
            'nota_grado' => $pg->nota,
        ];
    }

    public function documentosEstudiante($estudiante_id)
    {
        $documentos = Variables::documentos();
        $estudiante = $this->getEstudiante($estudiante_id);

        if (!$estudiante) return response('No permitido', 400);

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
                'motivo_rechazo' => $ed->motivo_rechazo,
                'can_generar' => $ed->can_generar,
                'can_show' => $ed->can_show,
                'can_aprobar' => $ed->can_aprobar,
                'can_rechazar' => $ed->can_rechazar,
                'can_cargar' => $ed->can_cargar_direccion,
            ]);
        }

        return $res;
    }

    public function generarDocumento($ed_id)
    {
        $ed = EstudianteDocumento::find($ed_id);
        if (!$ed->can_generar) return response('Este documento no se puede generar', 400);

        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante_ids = $ur->usuario->estudiantes_coordinados->get()->pluck('id')->toArray();

        if (!in_array($ed->idEstudiante, $estudiante_ids)) return response('No permitido', 400);

        $documentos = Variables::documentos();
        $estados = Variables::estados();
        $success = false;

        switch ($ed->idDocumento) {
            case $documentos['paz_salvos']->id:
                $success = DocumentoHelper::generarPazSalvo($ed);
                break;

            case $documentos['ayre']->id:
                $success = DocumentoHelper::generarAdmisiones($ed);
                break;

            case $documentos['ficha']->id:
                $success = DocumentoHelper::generarFicha($ed);
                break;

            case $documentos['identificacion']->id:
                $success = DocumentoHelper::actualizarDocumentoIdentidad($ed);
                break;
        }

        if ($success) {
            $ed->estado_id = $estados['aprobado']->id;
            $ed->url_documento = $ed->path;
            $ed->motivo_rechazo = null;
            $ed->user_update = $ur->usuario->identificacion;
            $ed->fecha_update = Carbon::now();
            $ed->save();
        }


        return 'ok';
    }

    public function aprobarDocumento(Request $request)
    {
        $this->validate($request, ['documento_id' => 'required|exists:estudiante_documento,id']);

        $ed = EstudianteDocumento::find($request->documento_id);
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante_ids = $ur->usuario->estudiantes_coordinados->get()->pluck('id')->toArray();

        if (!in_array($ed->idEstudiante, $estudiante_ids)) return response('No permitido', 400);

        $estados = Variables::estados();
        $ed->estado_id = $estados['aprobado']->id;
        $ed->motivo_rechazo = null;
        $ed->user_update = $ur->usuario->identificacion;
        $ed->fecha_update = Carbon::now();

        $ed->save();
        return 'ok';
    }

    public function rechazarDocumento(Request $request)
    {
        $this->validate($request, [
            'documento_id' => 'required|exists:estudiante_documento,id',
            'motivo' => 'required'
        ]);

        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);
        $ed = EstudianteDocumento::find($request->documento_id);

        if ($ur->rol_id === $roles['coordinador']->id) {
            $estudiante_ids = $ur->usuario->estudiantes_coordinados->get()->pluck('id')->toArray();
            if (!in_array($ed->idEstudiante, $estudiante_ids)) return response('No permitido', 400);
        }

        $estados = Variables::estados();
        $ed->estado_id = $estados['rechazado']->id;
        $ed->motivo_rechazo = $request->motivo;
        $ed->user_update = null;
        $ed->fecha_update = null;
        $ed->save();

        $pg = $ed->estudiante->procesoGrado;
        $pg->estado_programa_id = $estados['pendiente']->id;
        $pg->fecha_programa = null;
        $pg->motivo_no_aprobado = null;
        $pg->save();

        return 'ok';
    }

    public function aprobarEstudiante(AprobarEstudianteRequest $request)
    {
        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $this->getEstudiante($request->estudiante_id);

        if (!$estudiante) return response('No permitido', 400);
        if (!$estudiante->can_aprobar) return response('El estudiante no está apto para aprobar', 400);

        $estados = Variables::estados();
        $pg = $estudiante->procesoGrado;

        switch ($ur->rol_id) {
            case $roles['coordinador']->id:
                $pg->estado_programa_id = $estados['aprobado']->id;
                $pg->fecha_programa = Carbon::now();
                break;

            case $roles['secretariaGeneral']->id:
                $pg->estado_secretaria_id = $estados['aprobado']->id;
                $pg->fecha_secretaria = Carbon::now();
                break;
        }

        $pg->motivo_no_aprobado = null;
        $pg->save();

        return 'ok';
    }

    public function noAprobarEstudiante(AprobarEstudianteRequest $request)
    {
        $this->validate($request, [
            'motivo' => 'required'
        ], [
            '*.required' => 'Obligatorio'
        ]);

        $estudiante = $this->getEstudiante($request->estudiante_id);
        if (!$estudiante) return response('No permitido', 400);

        $pg = $estudiante->procesoGrado;
        $pg->no_aprobado = true;
        $pg->motivo_no_aprobado = $request->motivo;
        $pg->save();

        return 'ok';
    }

    public function actualizarEstudiante($estudiante_id)
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->estudiantes_coordinados->find($estudiante_id);

        if (!$estudiante) return response('No permitido', 400);

        $ws = new WSAdmisiones();
        $persona = $estudiante->persona;
        $data = $ws->getInformacionGraduadoByDocumentoIdentidad($persona->identificacion)[0];
        $tipoDoc = TipoDocumento::where('abrv', $data->tipoDocumento)->first();
        $ciudadOrigen = Municipio::where('nombre', $data->ciudad)->first();

        $persona->nombres = $data->nombres;
        $persona->apellidos = $data->apellidos;
        $persona->tipodoc = $tipoDoc->id;
        $persona->identificacion = $data->numeroDocumento;
        $persona->ciudadExpedicion = $data->ciudadCedula;
        $persona->ciudadOrigen = $ciudadOrigen ? $ciudadOrigen->id : null;
        $persona->fechaNacimiento = Carbon::createFromFormat('d/m/Y', $data->fechaNacimiento);
        $persona->save();

        $proceso = $estudiante->procesoGrado;
        $proceso->titulo_grado = $data->tituloProfesional;
        $proceso->titulo_memoria_grado = $data->descripcionOpcionGrado;
        $proceso->nota = $data->notaOpcionGrado;
        $proceso->modalidad_grado = $data->opcionGrado;
        $proceso->save();

        return 'ok';
    }

    public function getInfoAdicionalEstudiante($estudiante_id)
    {
        $estudiante = $this->getEstudiante($estudiante_id);
        if (!$estudiante) return response('No permitido', 400);

        $pg = $estudiante->procesoGrado;
        return [
            'estudiante_id' => $estudiante->id,
            'codigo_ecaes' => $pg->codigo_ecaes,
            'mejor_ecaes' => $pg->mejor_ecaes,
            'mencion_honor' => $pg->mencion_honor,
            'incentivo_nacional' => $pg->incentivo_nacional,
            'incentivo_institucional' => $pg->incentivo_institucional,
        ];
    }

    public function infoAdicionalEstudiante(Request $request)
    {
        $this->validate($request, [
            'estudiante_id' => 'required|exists:estudiantes,id',
            // 'resultado_ecaes' => 'required',
            // 'titulo_memoria_grado' => 'required',
            'codigo_ecaes' => '',
            'incentivo_institucional' => '',
            'incentivo_nacional' => '',
            'mejor_ecaes' => '',
            'mencion_honor' => ''
        ], [
            '*.required' => 'Obligatorio'
        ]);

        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->estudiantes_coordinados->find($request->estudiante_id);

        if (!$estudiante) return response('No permitido', 400);

        $pg = $estudiante->procesoGrado;
        // $pg->resultado_ecaes = $request->resultado_ecaes;
        // $pg->titulo_memoria_grado = $request->titulo_memoria_grado;
        if ($request->incentivo_institucional !== null) $pg->incentivo_institucional = $request->incentivo_institucional;
        if ($request->incentivo_nacional !== null) $pg->incentivo_nacional = $request->incentivo_nacional;
        if ($request->mejor_ecaes !== null) $pg->mejor_ecaes = $request->mejor_ecaes;
        if ($request->mencion_honor !== null) $pg->mencion_honor = $request->mencion_honor;
        if ($request->codigo_ecaes !== null) $pg->codigo_ecaes = $request->codigo_ecaes;

        $pg->save();

        return 'ok';
    }
}
