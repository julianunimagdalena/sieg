<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargaRequest;
use App\Http\Requests\FechaGradoRequest;
use App\Models\DependenciaModalidad;
use App\Models\Persona;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;
use App\Models\Dependencia;
use App\Models\DependenciaPazSalvo;
use App\Models\FechaGrado;
use App\Models\PazSalvo;

class AdminController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();

        // egresados admin
        session(['ur' => UsuarioRol::find(11)]);
        \Illuminate\Support\Facades\Auth::login(session('ur')->usuario);

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
            $programas = [];

            if ($ur->rol_id === $roles['coordinador']->id) {
                foreach ($ur->usuario->dependenciasModalidades as $dm) {
                    if (!in_array($dm->programa->nombre, $programas)) array_push($programas, $dm->programa->nombre);
                }
            }

            array_push($res, [
                'id' => $ur->id,
                'username' => $ur->usuario->identificacion,
                'identificacion' => $ur->usuario->persona->identificacion,
                'rol' => $ur->rol->nombre,
                'programas' => implode(', ', $programas)
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

        return [
            'id' => $ur ? $ur->id : null,
            'activo' => $ur ? $ur->activo : null,
            'rol_id' => $ur ? $ur->rol_id : null,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'identificacion' => $persona->identificacion,
            'username' => $usuario->identificacion,
            'programa_ids' => $programas
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

        return [
            'paz_salvos' => $paz_salvos,
            'carga_ecaes' => $dependencia->carga_ecaes,
            'carga_titulo_grado' => $dependencia->carga_titulo_grado
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

        return 'ok';
    }

    public function cargaTituloGrado(CargaRequest $request)
    {
        $tipos = Variables::tiposDependencia();
        $dependencia = Dependencia::where('idTipo', $tipos['dir_programa']->id)->find($request->programa_id);

        if (!$dependencia) return response('No valido', 400);

        $dependencia->carga_titulo_grado = $request->value;
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

        if ($request->paz_salvo_id) $paz_salvo = PazSalvo::find($request->paz_salvo_id);
        else {
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
        $programa->save();

        $ps_ids = array_values(array_map(fn ($ps) => $ps->id, Variables::defaultPazSalvos()));
        $programa->pazSalvosNecesarios()->attach($ps_ids);

        $dm = new DependenciaModalidad();
        $dm->idPrograma = $programa->id;
        $dm->idFacultad = $request->facultad_id;
        $dm->idModalidad = $request->modalidad_id;
        $dm->idJornada = $request->jornada_id;
        $dm->save();

        return 'ok';
    }
}
