<?php

namespace App\Http\Controllers;

use App\Models\DependenciaModalidad;
use App\Models\Persona;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;

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
}
