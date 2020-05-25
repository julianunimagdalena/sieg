<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstudioRequest;
use App\Http\Requests\ExperienciaLaboralRequest;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

use App\Http\Requests\PersonaRequest;
use App\Models\Asociacion;
use App\Models\Concejo;
use App\Models\Discapacidad;
use App\Models\Distincion;
use App\Models\Estudio;
use App\Models\ExperienciaLaboral;
use App\Models\HojaVida;
use App\Models\HojaVidaIdioma;
use App\Tools\PersonaHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EstudianteController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();
        // josemartinezar estudiante
        // session(['ur' => UsuarioRol::find(10026)]);
        // danielviloriaap estudiante
        session(['ur' => UsuarioRol::find(20026)]);
        \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['estudiante']->nombre);
    }

    public function datos(Request $req)
    {
        $persona = session('ur')->usuario->persona;

        return [
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'genero_id' => $persona->idGenero,
            'etnia' => $persona->etnia,
            'tipo_documento_id' => $persona->tipodoc,
            'identificacion' => $persona->identificacion,
            'lugar_expedicion_documento' => $persona->ciudadExpedicion,
            'fecha_expedicion_documento' => $persona->fecha_expedicion,
            'pais_nacimiento_id' => $persona->municipioNacimiento->departamento->idPais,
            'departamento_nacimiento_id' => $persona->municipioNacimiento->idDepartamento,
            'municipio_nacimiento_id' => $persona->ciudadOrigen,
            'fecha_nacimiento' => $persona->fechaNacimiento,
            'estado_civil_id' => $persona->idEstadoCivil,
            'estrato' => $persona->estrato,
            'pais_residencia_id' => $persona->municipioResidencia->departamento->idPais,
            'departamento_residencia_id' => $persona->municipioResidencia->idDepartamento,
            'municipio_residencia_id' => $persona->ciudadResidencia,
            'direccion' => $persona->direccion,
            'barrio' => $persona->sector,
            'telefono_fijo' => $persona->telefono_fijo,
            'celular' => $persona->celular,
            'celular2' => $persona->celular2,
            'correo' => $persona->correo,
            'correo2' => $persona->correo2,
            'correo_institucional' => $persona->correo_institucional
        ];
    }

    public function datosAcademicos(Request $req)
    {
        $tipos = Variables::tiposEstudiante();
        $ur = UsuarioRol::find(session('ur')->id);
        $persona = $ur->usuario->persona;

        $info_grado = [];
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

            array_push($programas, [
                'codigo' => $est->codigo,
                'programa' => $estudio->programa->nombre,
                'facultad' => $estudio->facultad->nombre,
                'modalidad' => $estudio->modalidad->nombre
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
                    'mes_culminacion' => $est->mesGrado
                ]);
            }
        }

        return compact('info_grado', 'programas', 'info_academica');
    }

    public function perfil()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->usuario->persona->hojaVida->perfil;
    }

    public function distinciones()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->usuario->persona->hojaVida->distinciones;
    }

    public function asociaciones()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->usuario->persona->hojaVida->asociaciones;
    }

    public function concejos()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->usuario->persona->hojaVida->concejos;
    }

    public function discapacidades()
    {
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->usuario->persona->hojaVida->discapacidades;
    }

    public function idiomas()
    {
        $res = [];
        $ur = UsuarioRol::find(session('ur')->id);

        foreach ($ur->usuario->persona->hojaVida->idiomas as $idm) {
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
        $actualidad_laboral = null;
        $experiencias = [];
        $ur = UsuarioRol::find(session('ur')->id);
        $hoja = $ur->usuario->persona->hojaVida;

        if ($hoja) {
            // return $hoja;
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
                    'funciones' => $exp->funcioneslogros
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
        $ur = UsuarioRol::find(session('ur')->id);
        PersonaHelper::actualizarProgresoFicha($ur->usuario->persona);
    }

    public function guardarDatosPersonales(PersonaRequest $request)
    {
        $persona = session('ur')->usuario->persona;

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
        $persona->save();

        $this->actualizarProgresoFicha();
        return 'ok';
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

        $hv->concejos()->attach($concejo->id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function eliminarConcejo(Request $request)
    {
        $this->validate($request, ['id' => 'exists:concejos_profesionales,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $concejo = $hv->concejoes()->find($request->id);

        if (!$concejo) return response('no encontrado', 404);

        $hv->concejos()->detach($concejo->id);
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

        $hv->discapacidades()->attach($discapacidad->id);
        $this->actualizarProgresoFicha();

        return 'ok';
    }

    public function eliminarDiscapacidad(Request $request)
    {
        $this->validate($request, ['discapacidad_id' => 'exists:discapacidades,id']);

        $ur = UsuarioRol::find(session('ur')->id);
        $hv = $ur->usuario->persona->hojaVida;
        $discapacidad = $hv->discapacidades()->find($request->discapacidad_id);

        if (!$discapacidad) return response('no encontrado', 404);

        $hv->discapacidades()->detach($discapacidad->id);
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
        $ur = UsuarioRol::find(session('ur')->id);
        return $ur->persona->progreso_ficha;
    }

    public function infoGrado()
    {
        $res = [];
        $tipos = Variables::tiposEstudiante();
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiantes = $ur->usuario->persona->estudiantes()
            ->where('idTipo', $tipos['egresado']->id)
            ->get();

        foreach ($estudiantes as $est) {
            $paz_salvos = [];

            foreach ($est->estudiantePazSalvo as $eps) {
                array_push($paz_salvos, [
                    'nombre' => $eps->pazSalvo->nombre,
                    'dependencia' => $eps->pazSalvo->dependencia->nombre,
                    'comentario' => $eps->comentario,
                    'paz_salvo' => $eps->paz_salvo,
                ]);
            }

            array_push($res, [
                'nombre' => $est->persona->nombre,
                'programa' => $est->estudio->programa->nombre,
                'codigo' => $est->codigo,
                'estado_encuesta' => $est->procesoGrado->estado_encuesta,
                'estado_ficha' => $est->procesoGrado->estado_encuesta,
                'estado_programa' => $est->procesoGrado->estado_encuesta,
                'estado_secretaria' => $est->procesoGrado->estado_encuesta,
                'confirmacion_ceremonia' => $est->procesoGrado->confirmacion_asistencia,
                'estado_documentos' => $est->estado_documentos,
                'paz_salvos' => $paz_salvos
            ]);
        }

        return $res;
    }

    public function cargaDocumentos()
    {
        return view('egresado.carga_documentos');
    }

    public function documentosGrado()
    {
        $res = [];
        $tipos = Variables::tiposEstudiante();
        $ur = UsuarioRol::find(session('ur')->id);
        $estudiantes = $ur->usuario->persona->estudiantes()
            ->where('idTipo', $tipos['egresado']->id)
            ->get();

        foreach ($estudiantes as $est) {
            $documentos = [];

            foreach ($est->estudianteDocumento as $ed) {
                array_push($documentos, [
                    'id' => $ed->id,
                    'nombre' => $ed->documento->nombre,
                    'estado' => $ed->estado->nombre
                ]);
            }

            array_push($res, [
                'nombre' => $est->persona->nombre,
                'programa' => $est->estudio->programa->nombre,
                'codigo' => $est->codigo,
                'documentos' => $documentos
            ]);
        }

        return $res;
    }

    public function cargarDocumento(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:estudiante_documento,id',
            'file' => 'required|file|mimetypes:application/pdf',
            'codigo' => 'required|exists:estudiantes,codigo'
        ], [
            'required' => 'Obligatorio'
        ]);

        $ur = UsuarioRol::find(session('ur')->id);
        $estudiante = $ur->usuario->persona->estudiantes()->where('codigo', $request->codigo)->first();

        if (!$estudiante) return response('Este codigo no pertenece a este estudiante', 400);

        $ed = $estudiante->estudianteDocumento()->find($request->id);
        if (!$ed) return response('Este documento no pertenece a este estudiante', 400);

        $estados = Variables::estados();
        Storage::put($ed->path, file_get_contents($request->file('file')->getRealPath()));
        $ed->estado_id = $estados['pendiente']->id;
        $ed->url_documento = $ed->path;
        $ed->save();

        return 'ok';
    }
}
