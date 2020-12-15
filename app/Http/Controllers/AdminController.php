<?php

namespace App\Http\Controllers;

use App\Exports\EncuestaExport;
use App\Http\Requests\CargaRequest;
use App\Http\Requests\EstudianteRequest;
use App\Http\Requests\FechaGradoRequest;
use App\Models\DependenciaModalidad;
use App\Models\Persona;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;
use App\Models\Dependencia;
use App\Models\DependenciaDocumento;
use App\Models\DependenciaPazSalvo;
use App\Models\Documento;
use App\Models\Estudiante;
use App\Models\FechaGrado;
use App\Models\Municipio;
use App\Models\PazSalvo;
use App\Models\TipoDocumento;
use App\Models\Genero;
use App\Models\ProcesoGrado;
use App\Models\ProcesoGradoEncuesta;
use App\Models\TipoGrado;
use App\Tools\WSAdmisiones;
use App\Tools\FechaHelper;
use App\Tools\StringManager;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        // egresados admin
        // session(['ur' => UsuarioRol::find(11)]);
        // \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['administrador']->nombre);
    }

    public function index()
    {
        return redirect('/administrador/administrar-usuarios');
    }

    public function administrarUsuarios()
    {
        return view('administrador.administrar_usuarios');
    }

    public function usuarios()
    {
        $res = [];
        $roles = Variables::roles();
        $rol_ids = array_map(fn ($r) => $r->id, $roles);
        $urs = UsuarioRol::whereIn('rol_id', $rol_ids)
            ->where('rol_id', '<>', $roles['estudiante']->id)
            ->get();

        foreach ($urs as $ur) {
            $dirige = [];

            if ($ur->rol_id === $roles['coordinador']->id) {
                foreach ($ur->usuario->dependenciasModalidades as $dm) {
                    if (!in_array($dm->programa->nombre, $dirige)) array_push($dirige, $dm->programa->nombre);
                }
            }

            if ($ur->rol_id === $roles['dependencia']->id) {
                foreach ($ur->usuario->dependencias as $dep) {
                    if (!in_array($dep->nombre, $dirige)) array_push($dirige, $dep->nombre);
                }
            }

            array_push($res, [
                'id' => $ur->id,
                'username' => $ur->usuario->identificacion,
                'identificacion' => $ur->usuario->persona->identificacion,
                'rol' => $ur->rol->nombre,
                'dirige' => implode(', ', $dirige)
            ]);
        }

        return $res;
    }

    public function usuario(UsuarioRequest $request)
    {
        $ur = UsuarioRol::find($request->id);
        $roles = Variables::roles();
        $usuario = $ur ? $ur->usuario : User::where('identificacion', $request->username)->first();

        if ($usuario) {
            $query = $usuario->usuarioRol()->where('rol_id', $request->rol_id);
            if ($ur) $query = $query->where('id', '<>', $ur->id);

            $count = $query->count();
            if ($count === 1) return response('El usuario ya posee este rol', 400);
        } else {
            $persona = new Persona();
            $persona->nombres = $request->nombres;
            $persona->apellidos = $request->apellidos;
            $persona->identificacion = $request->identificacion;
            $persona->correo_institucional = $request->username . '@unimagdalena.edu.co';
            $persona->save();

            $usuario = new User();
            $usuario->idPersona = $persona->id;
        }

        $usuario->identificacion = $request->username;
        $usuario->save();

        if (!$ur) $ur = new UsuarioRol();

        $ur->usuario_id = $usuario->id;
        $ur->rol_id = $request->rol_id;
        $ur->activo = $request->activo;
        $ur->save();

        if ($request->rol_id == $roles['coordinador']->id) {
            $dm_ids = DependenciaModalidad::whereIn('idPrograma', $request->programa_ids)->get()->pluck('id');
            $usuario->dependenciasModalidades()->detach();
            $usuario->dependenciasModalidades()->attach($dm_ids);
        }

        switch ($request->rol_id) {
            case $roles['coordinador']->id:
                $dm_ids = DependenciaModalidad::whereIn('idPrograma', $request->programa_ids)->get()->pluck('id');
                $usuario->dependenciasModalidades()->detach();
                $usuario->dependenciasModalidades()->attach($dm_ids);
                break;
            case $roles['dependencia']->id:
                $dependencia_ids = Dependencia::whereIn('id', $request->dependencia_ids)->get()->pluck('id');
                $usuario->dependencias()->detach();
                $usuario->dependencias()->attach($dependencia_ids);
                break;
        }

        return 'ok';
    }

    public function eliminarUsuario(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer|exists:usuario_rol,id']);

        $ur = UsuarioRol::find($request->id);
        $ur->delete();

        return 'ok';
    }

    public function datosUsuario(Request $request)
    {
        $roles = Variables::roles();
        $ur = null;
        $usuario = null;
        $persona = null;

        if ($request->ur_id) {
            $ur = UsuarioRol::find($request->ur_id);
            $usuario = $ur->usuario;
            $persona = $usuario->persona;
        } else if ($request->identificacion) {
            $persona = Persona::where('identificacion', $request->identificacion)->first();
            $usuario = $persona->usuario;
        }

        if (!$usuario) return response('No encontrado', 400);

        $programas = [];
        if ($ur && $ur->rol_id === $roles['coordinador']->id) {
            foreach ($usuario->dependenciasModalidades as $dm) {
                if (!in_array($dm->idPrograma, $programas)) array_push($programas, $dm->idPrograma);
            }
        }

        $dependencias = [];
        if ($ur && $ur->rol_id === $roles['dependencia']->id) {
            foreach ($usuario->dependencias as $dep) {
                if (!in_array($dep->id, $dependencias)) array_push($dependencias, $dep->id);
            }
        }

        return [
            'id' => $ur ? $ur->id : null,
            'activo' => $ur ? $ur->activo : null,
            'rol_id' => $ur ? $ur->rol_id : null,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'identificacion' => $persona->identificacion,
            'username' => $usuario->identificacion,
            'programa_ids' => $programas,
            'dependencia_ids' => $dependencias
        ];
    }

    public function fechaGrado($fecha_grado_id)
    {
        $fecha = FechaGrado::find($fecha_grado_id);

        return [
            'id' => $fecha->id,
            'fecha' => $fecha->fecha_grado,
            'nombre' => $fecha->nombre,
            'inscripcion_fecha_inicio' => $fecha->inscripcion_fecha_inicio,
            'inscripcion_fecha_fin' => $fecha->inscripcion_fecha_fin,
            'doc_est_fecha_fin' => $fecha->inscripcion_fecha_fin,
            'paz_salvo_fecha_fin' => $fecha->paz_salvo_fecha_fin,
            'direccion_prog_fecha_fin' => $fecha->direccion_prog_fecha_fin,
            'secretaria_gen_fecha_fin' => $fecha->secretaria_gen_fecha_fin,
            'tipo_grado_id' => $fecha->tipo_grado,
            'estado' => $fecha->estado,
            'observacion' => $fecha->observacion
        ];
    }

    public function editarFechaGrado(FechaGradoRequest $request)
    {
        $fecha = FechaGrado::where('fecha_grado', $request->fecha)->where('id', '<>', $request->id)->first();
        if ($fecha) return response('La fecha ya existe', 400);

        if ($request->id) {
            $fecha = FechaGrado::find($request->id);
            if (!$fecha) return response('Not found', 400);
        } else $fecha = new FechaGrado();

        $fecha->fecha_grado = $request->fecha;
        $fecha->nombre = $request->nombre;
        $fecha->inscripcion_fecha_inicio = $request->inscripcion_fecha_inicio;
        $fecha->inscripcion_fecha_fin = $request->inscripcion_fecha_fin;
        $fecha->doc_est_fecha_fin = $request->doc_est_fecha_fin;
        $fecha->paz_salvo_fecha_fin = $request->paz_salvo_fecha_fin;
        $fecha->direccion_prog_fecha_fin = $request->direccion_prog_fecha_fin;
        $fecha->secretaria_gen_fecha_fin = $request->secretaria_gen_fecha_fin;
        $fecha->tipo_grado = $request->tipo_grado_id;
        $fecha->estado = $request->estado;
        $fecha->observacion = $request->observacion;
        $fecha->anio = explode('-', $request->fecha)[0];
        $fecha->save();

        return 'ok';
    }

    public function eliminarFechaGrado(Request $request)
    {
        $this->validate($request, ['fecha_id' => 'required|exists:fechas_de_grado,id']);

        $fecha = FechaGrado::find($request->fecha_id);
        if ($fecha->procesosGrado()->count() > 0) return response('No se puede eliminar, hay estudiantes asignados a esta fecha de grado.', 400);

        $fecha->delete();
        return 'ok';
    }

    public function fechasGrado()
    {
        return view('administrador.fechas_grado');
    }

    public function estudiantes()
    {
        return view('secgeneral.estudiantes');
    }

    public function infoPrograma($programa_id)
    {
        $dependencia = Dependencia::find($programa_id);
        if (!$dependencia) return response('No valido', 400);

        $paz_salvos = $dependencia->dependenciaPazSalvo
            ->map(fn ($dps) => ['id' => $dps->id, 'nombre' => $dps->pazSalvo->nombre])
            ->all();

        $documentos = $dependencia->dependenciaDocumento
            ->map(fn ($dd) => [
                'id' => $dd->id,
                'nombre' => $dd->documento->nombre,
                'can_delete' => $dd->can_delete
            ])
            ->all();

        return [
            'paz_salvos' => $paz_salvos,
            'documentos' => $documentos,
            'carga_ecaes' => $dependencia->carga_ecaes,
            'carga_titulo_grado' => $dependencia->carga_titulo_grado,
            'diligencia_encuesta' => $dependencia->digita_encuesta
        ];
    }

    public function configuracionProgramas()
    {
        return view('administrador.programas');
    }

    public function cargaEcaes(CargaRequest $request)
    {
        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $dependencia->carga_ecaes = $request->value;
        $dependencia->save();

        $documentos = Variables::documentos();
        $ecaes = $dependencia->documentosNecesarios()->find($documentos['ecaes']->id);

        if ($dependencia->carga_ecaes && !$ecaes) $dependencia->documentosNecesarios()->attach($documentos['ecaes']->id);
        else if (!$dependencia->carga_ecaes && $ecaes) $dependencia->documentosNecesarios()->detach($documentos['ecaes']->id);

        return 'ok';
    }

    public function cargaTituloGrado(CargaRequest $request)
    {
        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $dependencia->carga_titulo_grado = $request->value;
        $dependencia->save();

        $documentos = Variables::documentos();
        $titulo_grado = $dependencia->documentosNecesarios()->find($documentos['titulo_grado']->id);

        if ($dependencia->carga_titulo_grado && !$titulo_grado) $dependencia->documentosNecesarios()->attach($documentos['titulo_grado']->id);
        else if (!$dependencia->carga_titulo_grado && $titulo_grado) $dependencia->documentosNecesarios()->detach($documentos['titulo_grado']->id);

        return 'ok';
    }

    public function diligenciaEncuesta(CargaRequest $request)
    {
        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $dependencia->digita_encuesta = $request->value;
        $dependencia->save();

        return 'ok';
    }

    public function nuevoPazSalvo(Request $request)
    {
        $this->validate($request, [
            'programa_id' => 'integer|required|exists:dependencias,id',
            'paz_salvo_id' => 'integer|exists:paz_salvos,id',
            'paz_salvo_nombre' => 'required_without:paz_salvo_id'
        ]);

        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $paz_salvo = null;

        if ($request->paz_salvo_id) {
            $paz_salvo = $dependencia->pazSalvosNecesarios()->find($request->paz_salvo_id);
            if ($paz_salvo) return response('Este paz y salvo ya se encuentra registrado.', 400);

            $paz_salvo = PazSalvo::find($request->paz_salvo_id);
        } else {
            $paz_salvo = new PazSalvo();
            $paz_salvo->nombre = $request->paz_salvo_nombre;
            $paz_salvo->save();
        }

        $dps = new DependenciaPazSalvo();
        $dps->dependencia_id = $dependencia->id;
        $dps->paz_salvo_id = $paz_salvo->id;
        $dps->save();

        return 'ok';
    }

    public function borrarPazSalvo(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|required|exists:dependencia_paz_salvo,id'
        ]);

        DependenciaPazSalvo::destroy($request->id);

        return 'ok';
    }

    public function registrarPrograma(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|unique:dependencias,nombre',
            'codigo' => 'required',
            'nivel_estudio_id' => 'required|exists:nivel_estudio,id',
            'facultad_id' => 'required|exists:dependencias,id',
            'modalidad_id' => 'required|exists:modalidades_estudio,id',
            'jornada_id' => 'required|exists:jornadas,id'
        ]);

        $tipos = Variables::tiposDependencia();
        $programa = new Dependencia();
        $programa->nombre = $request->nombre;
        $programa->idTipo = $tipos['dir_programa']->id;
        $programa->codigoPrograma = $request->codigo;
        $programa->idNivelestudio = $request->nivel_estudio_id;
        $programa->carga_ecaes = true;
        $programa->carga_titulo_grado = true;
        $programa->digita_encuesta = true;
        $programa->save();

        $ps_ids = array_values(array_map(fn ($ps) => $ps->id, Variables::defaultPazSalvos()));
        $doc_ids = array_values(array_map(fn ($doc) => $doc->id, Variables::documentos()));
        $programa->pazSalvosNecesarios()->attach($ps_ids);
        $programa->documentosNecesarios()->attach($doc_ids);

        $dm = new DependenciaModalidad();
        $dm->idPrograma = $programa->id;
        $dm->idFacultad = $request->facultad_id;
        $dm->idModalidad = $request->modalidad_id;
        $dm->idJornada = $request->jornada_id;
        $dm->save();

        return 'ok';
    }

    public function nuevoDocumento(Request $request)
    {
        $this->validate($request, [
            'programa_id' => 'integer|required|exists:dependencias,id',
            'documento_id' => 'integer|exists:documento,id',
            'documento_nombre' => 'required_without:documento_id',
            'documento_abrv' => 'required_without:documento_id'
        ]);

        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $documento = null;

        if ($request->documento_id) {
            $documento = $dependencia->documentosNecesarios()->find($request->documento_id);
            if ($documento) return response('Este documento ya se encuentra registrado.', 400);

            $documento = Documento::find($request->documento_id);
        } else {
            $documento = new Documento();
            $documento->nombre = $request->documento_nombre;
            $documento->abrv = $request->documento_abrv;
            $documento->save();
        }

        $dependencia->documentosNecesarios()->attach($documento->id);
        return 'ok';
    }

    public function borrarDocumento(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|required|exists:dependencia_documento,id'
        ]);

        $dd = DependenciaDocumento::find($request->id);
        if (!$dd->can_delete) return response('No permitido', 400);

        $dd->delete();

        return 'ok';
    }

    public function graduados()
    {
        return view('administrador.graduados');
    }

    public function obtenerGraduados(Request $request)
    {
        $this->validate($request, [
            'modalidad_id' => 'exists:modalidades_estudio,id',
            'genero_id' => 'exists:genero,id',
            'facultad_id' => 'exists:dependencias,id',
            'programa_id' => 'exists:dependencias,id',
            'tipo_grado_id' => 'exists:tipos_de_grados,id',
            'fecha_grado_id' => 'exists:fechas_de_grado,id',
            'fecha_inicial' => 'date',
            'fecha_final' => 'date'
        ]);

        $data = [];
        $tipos = Variables::tiposEstudiante();
        $search = $request->search['value'];

        $searchBy = [
            'identificacion' => 'personas.identificacion',
            'nombres' => 'personas.nombres',
            'apellidos' => 'personas.apellidos',
            'facultad' => 'facultad.nombre',
            'programa' => 'programa.nombre',
            'fecha_grado' => 'fg.fecha_grado'
        ];

        // DRAW VALUE
        $draw = (int) $request->draw;

        // RECORDS TOTAL
        $estudiantes = Estudiante::where('estudiantes.idTipo', $tipos['graduado']->id)
            ->whereHas('estudio', function ($dm) use ($request) {
                if ($request->modalidad_id) $dm->where('idModalidad', $request->modalidad_id);
                if ($request->facultad_id) $dm->where('idFacultad', $request->facultad_id);
                if ($request->programa_id) $dm->where('idPrograma', $request->programa_id);
            })
            ->whereHas('persona', function ($per) use ($request) {
                if ($request->genero_id) $per->where('idGenero', $request->genero_id);
            })
            ->whereHas('procesoGrado.fechaGrado', function ($fg) use ($request) {
                if ($request->tipo_grado_id) $fg->where('tipo_grado', $request->tipo_grado_id);
                if ($request->fecha_grado_id) $fg->where('id', $request->fecha_grado_id);
                if ($request->fecha_inicial) $fg->where('fecha_grado', '>=', $request->fecha_inicial);
                if ($request->fecha_final) $fg->where('fecha_grado', '<=', $request->fecha_final);
            });

        $recordsTotal = $estudiantes->count();

        // RECORDS FILTERED
        $estudiantes = $estudiantes->join('personas', 'personas.id', '=', 'estudiantes.idPersona')
            ->join('dependencias_modalidades as dm', 'dm.id', '=', 'estudiantes.idPrograma')
            ->join('dependencias as programa', 'programa.id', '=', 'dm.idPrograma')
            ->join('dependencias as facultad', 'facultad.id', '=', 'dm.idFacultad')
            ->join('proceso_grado as pg', 'pg.idEstudiante', '=', 'estudiantes.id')
            ->join('fechas_de_grado as fg', 'fg.id', '=', 'pg.idFecha')
            ->where(function ($query) use ($search, $searchBy) {
                foreach (array_values($searchBy) as $key => $prop) {
                    if ($key === 0) $query->where($prop, 'like', "%$search%");
                    else $query->orWhere($prop, 'like', "%$search%");
                }
            });

        $recordsFiltered = $estudiantes->count();

        // ORDER
        $order = $request->order[0];
        $orderColumn = $request->columns[$order['column']]['data'];
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
            ->skip($request->start)
            ->take($request->length)
            ->get();

        // CALCULATE DATA
        foreach ($estudiantes as $est) {
            $estudiante = Estudiante::find($est->id);
            $persona = $estudiante->persona;
            $estudio = $estudiante->estudio;

            array_push($data, [
                'DT_RowData' => [
                    'id' => $estudiante->id
                ],
                'identificacion' => $persona->identificacion,
                'nombres' => $persona->nombres,
                'apellidos' => $persona->apellidos,
                'facultad' => $estudio->facultad->nombre,
                'programa' => $estudio->programa->nombre,
                'fecha_grado' => $est->procesoGrado->fechaGrado->fecha_grado,
                'acciones' => ''
            ]);
        }

        return compact('data', 'draw', 'recordsTotal', 'recordsFiltered');
    }

    public function graduado($estudiante_id)
    {
        $estudiante = Estudiante::graduados()->find($estudiante_id);
        if (!$estudiante) return response('No encontrado', 404);

        $ur = UsuarioRol::find(session('ur')->id);
        session(['estudiante_id' => $estudiante->id]);

        return view('egresado.ficha', ['isAdmin' => $ur->isRol('administrador')]);
    }

    public function registrarGraduados(Request $request = null)
    {
        $this->validate($request, [
            'fecha_inicial' => 'date',
            'fecha_final' => 'date|gte:fecha_inicial'
        ], [
            '*.date' => 'El campo debe ser una fecha',
        ]);

        $ws = new WSAdmisiones();
        $results = $ws->getListaGraduadoByFechas($request->fecha_inicial, $request->fecha_final);
        $res = [
            'errors' => [],
            'ya_graduados' => 0,
            'registrados' => 0,
            'actualizados' => 0
        ];

        function addError($pos, $descripcion, $arr)
        {
            array_push($arr['errors'], [
                'pos' => $pos,
                'descripcion' => $descripcion
            ]);

            return $arr;
        }

        foreach ($results as $key => $result) {
            $programa = StringManager::eliminarTildes($result->nombreDelPrograma);
            $modalidad = $result->modalidad === 'POSTGRADOS' ? 'POSGRADO' : $result->modalidad;
            $dm = DependenciaModalidad::whereHas('programa', fn ($prg) => $prg->where('nombre', $programa))
                ->whereHas('facultad', fn ($fac) => $fac->where('nombre', $result->nombreFacultad))
                ->whereHas('modalidad', fn ($mod) => $mod->where('nombre', $modalidad))
                ->first();

            if (!$dm) {
                $text = 'No se encuentra dependencia modalidad asociado a este registro: ' . json_encode((object) [
                    'programa' => $programa,
                    'modalidad' => $modalidad,
                    'facultad' => $result->nombreFacultad
                ]);
                $res = addError($key, $text, $res);
                continue;
            }

            $data = null;
            $persona = Persona::where('identificacion', $result->numeroDocumento)->first();

            if (!$persona) {
                $data = $ws->getInformacionGraduadoByDocumentoIdentidad($result->numeroDocumento)[0];

                if (!$data) {
                    $res = addError($key, 'Error al endpoint getInformacionGraduadoByDocumentoIdentidad con este registro', $res);
                    continue;
                }

                $ciudadResidencia = Municipio::where('nombre', $data->ciudadResidencia)->first();
                $ciudadOrigen = Municipio::where('nombre', $data->ciudad)->first();
                $tipoDoc = TipoDocumento::where('abrv', $data->tipoDocumento)->first();
                $genero = $data->sexo;

                if ($genero === 'M') $genero = 'MASCULINO';
                else if ($genero === 'F') $genero = 'FEMENINO';
                else $genero = 'OTRO';

                $persona = new Persona();
                $persona->nombres = strtoupper($data->nombres);
                $persona->apellidos = strtoupper($data->apellidos);
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
                $persona->tipodoc = $tipoDoc ? $tipoDoc->id : null;
                $persona->save();
            }

            $tiposEstudiante = Variables::tiposEstudiante();
            $nuevo = false;
            $estudiante = Estudiante::where('idPersona', $persona->id)
                ->where('idPrograma', $dm->id)
                ->where('codigo', $result->codigoEstudiantil)
                ->first();

            if (!$estudiante) {
                $data2 = $ws->getInfoEstudianteByCodigo($result->codigoEstudiantil)[0];

                if ($data2->codigo === 'null') {
                    $res = addError($key, 'Falla endpoint getInfoEstudianteByCodigo con este registro', $res);
                    continue;
                }

                $nuevo = true;
                $zonal = $data2->zonal === 'NO DEFINIDO' ? 'SANTA MARTA' : $data2->zonal;
                $municipioZonal = Municipio::where('nombre', $zonal)->first();

                $estudiante = new Estudiante();
                $estudiante->codigo = $data2->codigo;
                $estudiante->idTipo = $tiposEstudiante['egresado']->id;
                $estudiante->idPrograma = $dm->id;
                $estudiante->idPersona = $persona->id;
                $estudiante->idZonal = $municipioZonal ? $municipioZonal->id : null;
                $estudiante->save();
            }

            if ($estudiante->idTipo === $tiposEstudiante['graduado']->id) {
                $res['ya_graduados']++;
                continue;
            }

            $fecha = Carbon::createFromFormat('d/m/Y', $result->fechaGrado);
            $fechaGrado = FechaGrado::where('fecha_grado', $fecha->toDateString())->first();

            if (!$fechaGrado) {
                $tipos = Variables::tiposGrado();
                $tipoGrado = TipoGrado::where('nombre', ucfirst(strtolower($result->tipoDeGruadiacion)))->first();

                $fechaGrado = new fechaGrado();
                $fechaGrado->anio = $fecha->year;
                $fechaGrado->estado = 0;
                $fechaGrado->fecha_grado = $fecha->toDateString();
                $fechaGrado->tipo_grado = $tipoGrado ? $tipoGrado->id : $tipos['no_reporta']->id;
                $fechaGrado->nombre = FechaHelper::nombreFecha($fechaGrado);
                $fechaGrado->save();
            }

            $estados = Variables::estados();
            $pg = $estudiante->procesoGrado ?? new ProcesoGrado();
            $pg->idEstudiante = $estudiante->id;
            $pg->idFecha = $fechaGrado->id;
            $pg->estado_secretaria_id = $estados['aprobado']->id;
            $pg->estado_programa_id = $estados['aprobado']->id;
            $pg->estado_ficha = 1;
            $pg->estado_encuesta = $dm->programa->digita_encuesta;
            $pg->titulo_grado = $result->tituloProfesional;
            $pg->modalidad_grado = $result->opcionGrado;
            $pg->titulo_memoria_grado = $result->descripcionOpcionGrado;
            $pg->nota = $result->notaOpcionGrado;
            $pg->no_aprobado = false;
            // $pg->motivo_no_aprobado = null;
            $pg->save();

            $estudiante->idTipo = $tiposEstudiante['graduado']->id;
            $estudiante->acta = $result->acta;
            $estudiante->folio = $result->folio;
            $estudiante->libro = $result->libro;
            $estudiante->save();

            $res[$nuevo ? 'registrados' : 'actualizados']++;
        }

        return $res;
    }

    public function registrarGraduado()
    {
        return view('egresado.ficha', ['register' => true]);
    }

    public function consultarGraduado(Request $request)
    {
        $this->validate($request, [
            'identificacion' => 'required'
        ], [
            '*.required' => 'Obligatorio'
        ]);

        $res = [];
        $persona = Persona::where('identificacion', $request->identificacion)->first();

        if ($persona) {
            $ctrl = new EstudianteController();
            $res = [
                'id' => $persona->id,
                'datos' => $ctrl->datos($persona)
            ];
        }

        return $res;
    }

    public function consultarGraduadoProgramas(Request $request)
    {
        $this->validate($request, [
            'persona_id' => 'required|exists:personas,id'
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $res = ['programas' => []];
        $persona = Persona::find($request->persona_id);

        if (!$persona) return response('No valido', 400);

        foreach ($persona->estudiantes as $estudiante) {
            $dm = $estudiante->estudio;
            $pg = $estudiante->procesoGrado;

            array_push($res['programas'], [
                'id' => $estudiante->id,
                'codigo' => $estudiante->codigo,
                'folio' => $estudiante->folio,
                'acta' => $estudiante->acta,
                'libro' => $estudiante->libro,
                'distincion_id' => $estudiante->distincion,
                'fecha_grado' => $pg ? $pg->fechaGrado->fecha_grado : null,
                'facultad_id' => $dm->idFacultad,
                'programa_id' => $dm->idPrograma,
                'jornada_id' => $dm->idJornada,
                'modalidad_id' => $dm->idModalidad,
                'resolve' => [
                    'programa' => $dm->programa->nombre,
                    'facultad' => $dm->facultad->nombre,
                    'modalidad' => $dm->modalidad->nombre,
                    'fecha_grado' => $pg ? $pg->fechaGrado->fecha_grado : null,
                ]
            ]);
        }

        return $res;
    }

    public function updateGraduado(EstudianteRequest $request)
    {
        $persona = Persona::find($request->persona_id);
        $estudiante = $request->id ? $persona->estudiantes()->find($request->id) : new Estudiante();

        if (!$estudiante) return response('No valido', 400);

        $dm = DependenciaModalidad::where('idPrograma', $request->programa_id)
            ->where('idFacultad', $request->facultad_id)
            ->where('idJornada', $request->jornada_id)
            ->where('idModalidad', $request->modalidad_id)
            ->first();

        if (!$dm) return response('Programa no valido', 400);

        $tipos = Variables::tiposEstudiante();
        $estudiante->codigo = $request->codigo;
        $estudiante->folio = $request->folio;
        $estudiante->acta = $request->acta;
        $estudiante->libro = $request->libro;
        $estudiante->distincion = $request->distincion_id;
        $estudiante->idPrograma = $dm->id;
        $estudiante->idPersona = $persona->id;
        $estudiante->idTipo = $tipos['graduado']->id;
        $estudiante->save();

        return 'ok';
    }

    public function descargarEncuesta(Request $request)
    {
        $this->validate($request, [
            'key' => 'required',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ]);

        $encuesta = Variables::encuestas($request->key);
        if (!$encuesta) return response('No se encuentra la encuesta', 400);

        $pges = ProcesoGradoEncuesta
            ::whereHas('procesoGrado.fechaGrado', function ($fg) use ($request) {
                if ($request->fecha_inicio) $fg->whereDate('fecha_grado', '>=', $request->fecha_inicio);
                if ($request->fecha_fin) $fg->whereDate('fecha_grado', '<=', $request->fecha_fin);
            })
            ->get();

        $export = new EncuestaExport($encuesta, $pges);
        return $export->download('encuesta.xlsx');
    }
}
