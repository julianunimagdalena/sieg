<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\DependenciaModalidad;
use App\Models\Persona;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

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
        $roles = Variables::roles();

        if ($request->id) $ur = UsuarioRol::find($request->id);
        else $ur = new UsuarioRol();

        $usuario = $ur->usuario;

        if ($usuario) {
            $rol = $usuario->usuarioRol()->where('rol_id', $request->rol_id)->first();
            if ($rol) return response('El usuario ya posee este rol', 400);
        } else {
            $persona = new Persona();
            $persona->nombres = $request->nombres;
            $persona->apellidos = $request->apellidos;
            $persona->identificacion = $request->identificacion;
            $persona->correo_institucional = $request->username . '@unimagdalena.edu.co';
            $persona->save();

            $usuario = new User();
            $usuario->idPersona = $persona->id;
            $usuario->identificacion = $request->username;
            $usuario->save();
        }

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

    public function datosUsuario($ur_id)
    {
        $ur = UsuarioRol::find($ur_id);
        $usuario = $ur->usuario;
        $persona = $usuario->persona;

        $programas = [];
        foreach ($usuario->dependenciasModalidades as $dm) {
            if (!in_array($dm->idPrograma, $programas)) array_push($programas, $dm->idPrograma);
        }

        return [
            'id' => $ur->id,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'identificacion' => $persona->identificacion,
            'username' => $usuario->identificacion,
            'activo' => $ur->activo,
            'rol_id' => $ur->rol_id,
            'programa_ids' => $programas
        ];
    }
}
