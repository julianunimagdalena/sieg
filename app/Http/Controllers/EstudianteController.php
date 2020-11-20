<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsistenciaCeremoniaRequest;
use App\Http\Requests\EstudioRequest;
use App\Http\Requests\ExperienciaLaboralRequest;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

use App\Http\Requests\PersonaRequest;
use App\Models\Asociacion;
use App\Models\Distincion;
use App\Models\Encuesta;
use App\Models\Estudiante;
use App\Models\EstudianteDocumento;
use App\Models\Estudio;
use App\Models\ExperienciaLaboral;
use App\Models\HojaVida;
use App\Models\HojaVidaIdioma;
use App\Models\Persona;
use App\Models\ProcesoGrado;
use App\Tools\PersonaHelper;
use App\Tools\WSFoto;
use Illuminate\Support\Facades\Storage;

class EstudianteController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();
        // josemartinezar estudiante
        // session(['ur' => UsuarioRol::find(10026)]);
        // danielviloriaap estudiante
        // session(['ur' => UsuarioRol::find(20026)]);
        // session(['estudiante_id' => 27300]);
        // \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['estudiante']->nombre, ['except' => [
            'datos',
            'datosAcademicos',
            'perfil',
            'distinciones',
            'asociaciones',
            'concejos',
            'discapacidades',
            'progresoFicha',
            'idiomas',
            'datosLaborales',
            'guardarDatosPersonales'
        ]]);
        $this->middleware('rol:' . $roles['estudiante']->nombre . '|' . $roles['administrador']->nombre, ['only' => [
            'datos',
            'datosAcademicos',
            'perfil',
            'distinciones',
            'asociaciones',
            'concejos',
            'discapacidades',
            'progresoFicha',
            'idiomas',
            'datosLaborales',
            'guardarDatosPersonales'
        ]]);
    }

    private function getPersona()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $roles = Variables::roles();
        $persona = null;

        switch ($ur->rol_id) {
            case $roles['estudiante']->id:
                $persona = $ur->usuario->persona;
                break;

            case $roles['administrador']->id:
                if (session('estudiante_id')) $persona = Estudiante::find(session('estudiante_id'))->persona;
                break;
        }

        return $persona;
    }

    public function datos($persona = null)
    {
        $persona = $persona ?? $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return [
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'genero_id' => $persona->idGenero,
            'etnia' => $persona->etnia,
            'tipo_documento_id' => $persona->tipodoc,
            'identificacion' => $persona->identificacion,
            'lugar_expedicion_documento' => $persona->ciudadExpedicion,
            'fecha_expedicion_documento' => $persona->fecha_expedicion,
            'pais_nacimiento_id' => $persona->municipioNacimiento ? $persona->municipioNacimiento->departamento->idPais : null,
            'departamento_nacimiento_id' => $persona->municipioNacimiento ? $persona->municipioNacimiento->idDepartamento : null,
            'municipio_nacimiento_id' => $persona->ciudadOrigen,
            'fecha_nacimiento' => $persona->fechaNacimiento,
            'estado_civil_id' => $persona->idEstadoCivil,
            'estrato' => $persona->estrato,
            'pais_residencia_id' => $persona->municipioResidencia ? $persona->municipioResidencia->departamento->idPais : null,
            'departamento_residencia_id' => $persona->municipioResidencia ? $persona->municipioResidencia->idDepartamento : null,
            'municipio_residencia_id' => $persona->ciudadResidencia,
            'direccion' => $persona->direccion,
            'barrio' => $persona->sector,
            'telefono_fijo' => $persona->telefono_fijo,
            'celular' => $persona->celular,
            'celular2' => $persona->celular2,
            'correo' => $persona->correo,
            'correo2' => $persona->correo2,
            'correo_institucional' => $persona->correo_institucional,
            'estado_vida' => $persona->estadovida
        ];
    }

    public function datosAcademicos(Request $req)
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        $info_grado = [];
        $tipos = Variables::tiposEstudiante();
        $estudiantesEgresados = $persona->estudiantes()
            ->where('idTipo', $tipos['egresado']->id)
            ->get();

        foreach ($estudiantesEgresados as $est) {
            $estudio = $est->estudio;

            array_push($info_grado, [
                'codigo' => $est->codigo,
                'programa' => $estudio->programa->nombre,
                'facultad' => $estudio->facultad->nombre,
                'modalidad' => $estudio->modalidad->nombre
            ]);
        }

        $programas = [];
        $estudiantesGraduados = $persona->estudiantes()
            ->where('idTipo', $tipos['graduado']->id)
            ->get();

        foreach ($estudiantesGraduados as $est) {
            $estudio = $est->estudio;
            $pg = $est->procesoGrado;

            array_push($programas, [
                'codigo' => $est->codigo,
                'programa' => $estudio->programa->nombre,
                'facultad' => $estudio->facultad->nombre,
                'modalidad' => $estudio->modalidad->nombre,
                'fecha_grado' => $pg ? $pg->fechaGrado->fecha_grado : ''
            ]);
        }

        $info_academica = [];
        $hv = $persona->hojaVida;

        if ($hv) {
            foreach ($hv->estudios as $est) {
                array_push($info_academica, [
                    'id' => $est->id,
                    'nombre' => $est->titulo,
                    'institucion' => $est->institucion,
                    'meses' => $est->duracion,
                    'graduado' => $est->graduado,
                    'anio_culminacion' => $est->anioGrado,
                    'mes_culminacion' => $est->mesGrado,
                    'nivel_estudio_id' => $est->nivel_estudio_id
                ]);
            }
        }

        return compact('info_grado', 'programas', 'info_academica');
    }

    public function perfil()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->hojaVida->perfil;
    }

    public function distinciones()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->hojaVida->distinciones;
    }

    public function asociaciones()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->hojaVida->asociaciones;
    }

    public function concejos()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->hojaVida->concejos;
    }

    public function discapacidades()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->hojaVida->discapacidades;
    }

    public function idiomas()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        $res = [];

        foreach ($persona->hojaVida->idiomas as $idm) {
            array_push($res, [
                'id' => $idm->id,
                'idioma_id' => $idm->idioma->id,
                'nivel_habla_id' => $idm->nivelHabla->id,
                'nivel_escritura_id' => $idm->nivelEscritura->id,
                'nivel_lectura_id' => $idm->nivelLectura->id
            ]);
        }

        return $res;
    }

    public function datosLaborales()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        $actualidad_laboral = null;
        $experiencias = [];
        $hoja = $persona->hojaVida;

        if ($hoja) {
            $actualidad_laboral = $hoja->laborando;

            foreach ($hoja->experiencias as $exp) {
                array_push($experiencias, [
                    'id' => $exp->id,
                    'empresa' => $exp->empresa,
                    'cargo' => $exp->cargo,
                    'nivel_cargo_id' => $exp->nivel_cargo_id,
                    'municipio_id' => $exp->municipio_id,
                    'departamento_id' => $exp->municipio->idDepartamento,
                    'pais_id' => $exp->municipio->departamento->idPais,
                    'duracion_id' => $exp->duracion,
                    'tipo_vinculacion_id' => $exp->tipo_vinculacion_id,
                    'salario_id' => $exp->salario_id,
                    'correo' => $exp->email,
                    'telefono' => $exp->telefono,
                    'contrato_activo' => $exp->contrato_activo,
                    'funciones' => $exp->funcioneslogros,
                    'sector_id' => $exp->sector_empresa_id,
                    'direccion' => $exp->direccion,
                    'sector_economico_id' => $exp->sector_economico_id,
                    'actividad_economica_id' => $exp->actividad_economica_id,
                    'area_desempeno_id' => $exp->area_desempeno_id,
                    'fecha_ingreso' => $exp->fecha_ingreso,
                    'fecha_retiro' => $exp->fecha_retiro,
                ]);
            }
        }

        return compact('actualidad_laboral', 'experiencias');
    }

    public function index()
    {
        return view('egresado.index');
    }

    public function fichaEgresado()
    {
        return view('egresado.ficha');
    }

    public function actualizarProgresoFicha()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('no permitido', 400);

        PersonaHelper::actualizarProgresoFicha($persona);
    }

    public function guardarDatosPersonales(PersonaRequest $request)
    {
        $persona = $this->getPersona();

        if (!$persona) {
            $ur = UsuarioRol::find(session('ur')->id);

            if ($ur->isRol('administrador')) {
                $persona = new Persona();
                $persona->nombres = $request->nombres;
                $persona->apellidos = $request->apellidos;
                $persona->etnia = $request->etnia;
                $persona->fechaNacimiento = $request->fecha_nacimiento;
                $persona->tipodoc = $request->tipo_documento_id;
                $persona->identificacion = $request->identificacion;
                $persona->ciudadExpedicion = $request->lugar_expedicion_documento;
                $persona->ciudadOrigen = $request->municipio_nacimiento_id;
                $persona->save();
            }
        }

        if (!$persona) return response('no permitido', 400);

        $persona->fecha_expedicion = $request->fecha_expedicion_documento;
        $persona->idEstadoCivil = $request->estado_civil_id;
        $persona->idGenero = $request->genero_id;
        $persona->direccion = $request->direccion;
        $persona->sector = $request->barrio;
        $persona->ciudadResidencia = $request->municipio_residencia_id;
        $persona->estrato = $request->estrato;
        $persona->telefono_fijo = $request->telefono_fijo;
        $persona->celular = $request->celular;
        $persona->celular2 = $request->celular2;
        $persona->correo = $request->correo;
        $persona->correo2 = $request->correo2;
        $persona->estadovida = $request->estado_vida !== null ? $request->estado_vida : true;
        $persona->save();

        $hv = $persona->hojaVida ?? new HojaVida();
        $hv->perfil = $hv->perfil ?? '-';
        $hv->laborando = $hv->laborando !== null ? $hv->laborando : 0;
        $hv->idPersona = $persona->id;
        $hv->save();

        $this->actualizarProgresoFicha();
        return ['id' => $persona->id];
    }

    public function guardarEstudio(EstudioRequest $request)
    {
        $estudio = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        if ($request->id) $estudio = $hv->estudios()->find($request->id);
        else $estudio = new Estudio();

        if (!$estudio) return response('No permitido', 401);

        $estudio->idHoja = $hv->id;
        $estudio->titulo = $request->nombre;
        $estudio->institucion = $request->institucion;
        $estudio->duracion = $request->meses;
        $estudio->nivel_estudio_id = $request->nivel_estudio_id;
        $estudio->graduado = $request->graduado;
        $estudio->anioGrado = $request->graduado ? $request->anio_culminacion : null;
        $estudio->mesGrado = $request->graduado ? $request->mes_culminacion : null;
        $estudio->save();

        return 'ok';
    }

    public function eliminarEstudio(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:estudiosrealizados,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $estudio = $hv->estudios()->find($request->id);

        if (!$estudio) return response('no permitido', 401);

        $estudio->delete();
        return 'ok';
    }

    public function editarPerfilProfesional(Request $request)
    {
        $this->validate($request, ['perfil' => 'required']);
        $ur = UsuarioRol::find(session('ur')->id);

        $hv = $ur->usuario->persona->hojaVida;
        $hv->perfil = $request->perfil;
        $hv->save();

        $this->actualizarProgresoFicha();
        return 'ok';
    }

    public function guardarDistincion(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:distinciones,id',
            'nombre' => 'required'
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $distincion = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        if ($request->id) $distincion = $hv->distinciones()->find($request->id);
        else $distincion = new Distincion();

        if (!$distincion) return response('no permitido', 401);

        $distincion->idHoja = $hv->id;
        $distincion->nombre = $request->nombre;
        $distincion->save();

        $this->actualizarProgresoFicha();
        return 'ok';
    }

    public function eliminarDistincion(Request $request)
    {
        $this->validate($request, ['id' => 'exists:distinciones,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $distincion = $hv->distinciones()->find($request->id);

        if (!$distincion) return response('no permitido', 401);

        $distincion->delete();
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function guardarAsociacion(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:asociaciones,id',
            'nombre' => 'required'
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $asociacion = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        if ($request->id) $asociacion = $hv->asociaciones()->find($request->id);
        else $asociacion = new Asociacion();

        if (!$asociacion) return response('no permitido', 401);

        $asociacion->idHoja = $hv->id;
        $asociacion->nombre = $request->nombre;
        $asociacion->save();

        $this->actualizarProgresoFicha();
        return 'ok';
    }

    public function eliminarAsociacion(Request $request)
    {
        $this->validate($request, ['id' => 'exists:asociaciones,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $asociacion = $ur->usuario->persona->hojaVida->asociaciones()->find($request->id);

        if (!$asociacion) return response('no permitido', 401);

        $asociacion->delete();
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function guardarConcejo(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:concejos_profesionales,id',
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $concejo = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        $concejo = $hv->concejos()->find($request->id);
        if ($concejo) return response('ya se encuentra este concejo registrada', 400);

        $hv->concejos()->attach($request->id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function eliminarConcejo(Request $request)
    {
        $this->validate($request, ['id' => 'exists:concejos_profesionales,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $concejo = $hv->concejos()->find($request->id);

        if (!$concejo) return response('no encontrado', 404);

        $hv->concejos()->detach($request->id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function agregarDiscapacidad(Request $request)
    {
        $this->validate($request, [
            'discapacidad_id' => 'required|exists:discapacidades,id'
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $discapacidad = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        $discapacidad = $hv->discapacidades()->find($request->discapacidad_id);
        if ($discapacidad) return response('ya se encuentra esta discapacidad registrada', 400);

        $hv->discapacidades()->attach($request->discapacidad_id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function eliminarDiscapacidad(Request $request)
    {
        $this->validate($request, ['id' => 'exists:discapacidades,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $discapacidad = $hv->discapacidades()->find($request->id);

        if (!$discapacidad) return response('no encontrado', 404);

        $hv->discapacidades()->detach($request->id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function guardarIdioma(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:hojadevida_idiomas,id',
            'idioma_id' => 'required|exists:idiomas,id',
            'nivel_habla_id' => 'required|exists:niveles_idiomas,id',
            'nivel_escritura_id' => 'required|exists:niveles_idiomas,id',
            'nivel_lectura_id' => 'required|exists:niveles_idiomas,id'
        ], [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ]);

        $hvi = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        if ($request->id) $hvi = $hv->idiomas()->find($request->id);
        else $hvi = new HojaVidaIdioma();

        if (!$hvi) return response('no permitido', 401);
        if (!$hvi->id && $hv->idiomas()->where('idIdioma', $request->idioma_id)->count() > 0)
            return response('Ya se encuentra registrado', 400);

        $hvi->idHoja = $hv->id;
        $hvi->idIdioma = $request->idioma_id;
        $hvi->lectura = $request->nivel_lectura_id;
        $hvi->escritura = $request->nivel_escritura_id;
        $hvi->habla = $request->nivel_habla_id;
        $hvi->save();

        return 'ok';
    }

    public function eliminarIdioma(Request $request)
    {
        $this->validate($request, ['id' => 'exists:hojadevida_idiomas,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $idioma = $hv->idiomas()->find($request->id);

        if (!$idioma) return response('no permitido', 401);

        $idioma->delete();
        return 'ok';
    }

    public function guardarActualidadLaboral(Request $request)
    {
        $this->validate($request, ['laborando' => 'required|boolean']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $hv->laborando = $request->laborando;

        $hv->save();
        $this->actualizarProgresoFicha();

        return [$hv->laborando];
    }

    public function guardarExperiencia(ExperienciaLaboralRequest $request)
    {
        $experiencia = null;
        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;

        if ($request->id) $experiencia = $hv->experiencias()->find($request->id);
        else $experiencia = new ExperienciaLaboral();

        if (!$experiencia) return response('no permitido', 401);

        $experiencia->idHoja = $hv->id;
        $experiencia->empresa = $request->empresa;
        $experiencia->cargo = $request->cargo;
        $experiencia->nivel_cargo_id = $request->nivel_cargo_id;
        $experiencia->municipio_id = $request->municipio_id;
        $experiencia->duracion = $request->duracion_id;
        $experiencia->tipo_vinculacion_id = $request->tipo_vinculacion_id;
        $experiencia->salario_id = $request->salario_id;
        $experiencia->email = $request->correo;
        $experiencia->telefono = $request->telefono;
        $experiencia->funcioneslogros = $request->funciones;
        $experiencia->contrato_activo = $request->contrato_activo;
        $experiencia->sector_empresa_id = $request->sector_id;
        $experiencia->direccion = $request->direccion;
        $experiencia->sector_economico_id = $request->sector_economico_id;
        $experiencia->actividad_economica_id = $request->actividad_economica_id;
        $experiencia->area_desempeno_id = $request->area_desempeno_id;
        $experiencia->fecha_ingreso = $request->fecha_ingreso;
        $experiencia->fecha_retiro = $request->fecha_retiro;
        $experiencia->save();

        return 'ok';
    }

    public function eliminarExperiencia(Request $request)
    {
        $this->validate($request, ['id' => 'exists:experiencias_laborales,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $experiencia = $hv->experiencias()->find($request->id);

        if (!$experiencia) return response('no permitido', 401);

        $experiencia->delete();
        return 'ok';
    }

    public function progresoFicha()
    {
        $persona = $this->getPersona();
        if (!$persona) return response('No permitido', 400);

        return $persona->progreso_ficha;
    }

    // DEMAS PETICIONES
    public function infoGrado()
    {
        $res = [];
        $paz_salvos = [];
        $estudiante = Estudiante::find(session('estudiante_id'));

        foreach ($estudiante->estudiantePazSalvo as $eps) {
            $ps = $eps->pazSalvo;
            $dependencia = $ps->dependencia;

            array_push($paz_salvos, [
                'nombre' => strtolower($ps->nombre),
                'dependencia' => $dependencia ? $dependencia->nombre : '-',
                'comentario' => $eps->comentario,
                'paz_salvo' => $eps->paz_salvo,
            ]);
        }

        array_push($res, [
            'nombre' => $estudiante->persona->nombre,
            'documento' => $estudiante->persona->identificacion,
            'tipo_documento' => $estudiante->persona->tipoDocumento->abrv,
            'programa' => $estudiante->estudio->programa->nombre,
            'codigo' => $estudiante->codigo,
            'estado_encuesta' => $estudiante->procesoGrado->estado_encuesta,
            'estado_ficha' => $estudiante->procesoGrado->estado_ficha,
            'estado_programa' => $estudiante->procesoGrado->estadoPrograma->nombre,
            'estado_secretaria' => $estudiante->procesoGrado->estadoSecretaria->nombre,
            'confirmacion_ceremonia' => $estudiante->procesoGrado->confirmacion_asistencia,
            'estado_documentos' => $estudiante->estado_documentos,
            'paz_salvos' => $paz_salvos,
            'diligencia_encuesta' => $estudiante->estudio->programa->digita_encuesta
        ]);

        return $res;
    }

    public function cargaDocumentos()
    {
        return view('egresado.carga_documentos');
    }

    public function documentosGrado()
    {
        $res = [];
        $documentos = [];
        $documentosGrado = Variables::documentos();
        $estudiante = Estudiante::find(session('estudiante_id'));

        $whitelist = [$documentosGrado['ecaes']->id, $documentosGrado['identificacion']->id];

        foreach ($whitelist as $doc_id) {
            $ed = $estudiante->estudianteDocumento()->where('idDocumento', $doc_id)->first();

            array_push($documentos, [
                'id' => $ed->id,
                'nombre' => $ed->documento->nombre,
                'estado' => $ed->estado->nombre,
                'motivo_rechazo' => $ed->motivo_rechazo,
                'can_show' => $ed->can_show,
                'can_upload' => $ed->can_cargar_estudiante,
                'is_ecaes' => $ed->idDocumento === $documentosGrado['ecaes']->id
            ]);
        }

        array_push($res, [
            'nombre' => $estudiante->persona->nombre,
            'programa' => $estudiante->estudio->programa->nombre,
            'codigo' => $estudiante->codigo,
            'documentos' => $documentos
        ]);

        return $res;
    }

    public function cargarDocumento(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer|exists:estudiante_documento,id']);

        $documentos = Variables::documentosGrado();
        $ed = EstudianteDocumento::find($request->id);

        if (!$ed->idEstudiante == session('estudiante_id')) return response('Este documento no pertenece a este estudiante', 400);

        $isEcaes = $ed->idDocumento === $documentos['ecaes']->id;
        $rules = ['file' => 'required|file|mimetypes:application/pdf'];

        if ($isEcaes) $rules['codigo_ecaes'] = 'required';
        $this->validate($request, $rules, ['required' => 'Obligatorio']);

        Storage::put($ed->path, file_get_contents($request->file('file')->getRealPath()));

        $estados = Variables::estados();
        $ed->estado_id = $estados['pendiente']->id;
        $ed->url_documento = $ed->path;
        $ed->motivo_rechazo = null;
        $ed->save();

        $pg = $ed->estudiante->procesoGrado;
        $pg->codigo_ecaes = $request->codigo_ecaes;
        $pg->save();

        return 'ok';
    }

    public function infoAsistenciaCeremonia($codigo)
    {
        $estudiante = Estudiante::find(session('estudiante_id'));
        $pg = $estudiante->procesoGrado;

        return [
            'pg_id' => $pg->id,
            'confirmacion_asistencia' => $pg->confirmacion_asistencia,
            'talla_camisa' => $pg->talla_camisa,
            'estatura' => $pg->estatura,
            'tamano_birrete' => $pg->tamano_birrete,
            'num_acompanantes' => $pg->num_acompaniantes
        ];
    }

    public function guardarAsistenciaCeremonia(AsistenciaCeremoniaRequest $request)
    {
        $ur = UsuarioRol::find(session('ur')->id);
        $pg = ProcesoGrado::whereHas('estudiante.persona', fn ($per) => $per->where('id', $ur->usuario->persona->id))
            ->find($request->pg_id);

        if (!$pg) return response('no encontrado', 400);

        $confirmacion = $request->confirmacion_asistencia;
        $pg->confirmacion_asistencia = $confirmacion;
        $pg->talla_camisa = $confirmacion ? $request->talla_camisa : null;
        $pg->estatura = $confirmacion ? $request->estatura : null;
        $pg->tamano_birrete = $confirmacion ? $request->tamano_birrete : null;
        $pg->num_acompaniantes = $confirmacion ? $request->num_acompanantes : null;
        $pg->save();

        return 'ok';
    }

    public function validarFoto(Request $request)
    {
        $ws = new WSFoto();
        $res = $ws->validarFoto($request->foto);

        return ['success' => $res->valido, 'descripcion' => $res->descripcion];
    }

    public function cargarFoto(Request $request)
    {
        $this->validate($request, [
            'aprobar' => 'boolean|required',
            'foto' => 'required_if:aprobar,false'
        ]);

        $res = true;
        $estudiante = Estudiante::find(session('estudiante_id'));
        $pg = $estudiante->procesoGrado;

        if ($request->aprobar) {
            $pg->foto_aprobada = true;
            $pg->save();
        } else {
            $ws = new WSFoto();
            $success = $ws->guardarFoto($request->foto, $estudiante);

            if ($success) {
                $pg->foto_cargada = true;
                $pg->save();
            } else $res = false;
        }

        return ['success' => $res];
    }

    public function datosFoto()
    {
        $estudiante = Estudiante::find(session('estudiante_id'));
        $pg = $estudiante->procesoGrado;

        return [
            'foto_aprobada' => $pg->foto_aprobada,
            'foto_cargada' => $pg->foto_cargada,
            'foto' => $estudiante->foto
        ];
    }

    private function respuestasPregunta($pregunta)
    {
        $respuestas = [];

        foreach ($pregunta->posiblesRespuestas as $respuesta) {
            array_push($respuestas, [
                'id' => $respuesta->id,
                'valor' => $respuesta->descripcion,
                'abierta' => $respuesta->abierta,
                'to_pregunta' => $respuesta->to_pregunta_id,
            ]);
        }

        return $respuestas;
    }

    public function encuesta($encuesta_id)
    {
        $encuesta = Encuesta::find($encuesta_id);
        $res = [
            'nombre' => $encuesta->nombre,
            'descripcion' => $encuesta->descripcion,
            'modulos' => []
        ];

        foreach ($encuesta->modulos as $modulo) {
            $preguntas = [];

            foreach ($modulo->preguntas()->noroot()->get() as $pregunta) {
                $subpreguntas = [];

                foreach ($pregunta->subpreguntas as $subpregunta) {
                    array_push($subpreguntas, [
                        'id' => $subpregunta->id,
                        'orden' => $subpregunta->orden,
                        'text' => $subpregunta->text,
                        'obligatoria' => $subpregunta->obligatorio,
                        'abierta' => $subpregunta->abierta,
                        'multiple' => $subpregunta->multiple,
                    ]);
                }

                array_push($preguntas, [
                    'id' => $pregunta->id,
                    'orden' => $pregunta->orden,
                    'text' => $pregunta->text,
                    'obligatoria' => $pregunta->obligatorio,
                    'abierta' => $pregunta->abierta,
                    'multiple' => $pregunta->multiple,
                    'respuestas' => $this->respuestasPregunta($pregunta),
                    'preguntas' => $subpreguntas
                ]);
            }

            array_push($res['modulos'], [
                'titulo' => $modulo->titulo,
                'descripcion' => $modulo->descripcion,
                'preguntas' => $preguntas
            ]);
        }

        return $res;
    }
}
