<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    public function autenticar(LoginRequest $req)
    {
        $rol = null;
        $client = new \GuzzleHttp\Client();
        $rolesPlatforma = Variables::roles();
        $ids = array_values(array_map(fn ($rol) => $rol->id, $rolesPlatforma));
        $user = User::where('identificacion', $req->username)->first();

        if (!$user) return response('Usuario no existe', 400);
        if (!$user->activo) return response('Usuario no activo', 400);

        if ($req->rol_id) {
            $rolObject = $user->roles()->whereIn('roles.id', $ids)->find($req->rol_id);
            $estudiante = $user->persona->estudiantes()->find($req->estudiante_id);
            $dependencia = $user->dependencias()->find($req->dependencia_id);

            if (
                ($rolObject->id === $rolesPlatforma['estudiante']->id && !$estudiante)
                || ($rolObject->id === $rolesPlatforma['dependencia']->id && !$dependencia)
            ) return response('Bad data', 400);

            $rol = [
                'id' => $rolObject->id,
                'estudiante_id' => $estudiante ? $estudiante->id : null,
                'dependencia_id' => $dependencia ? $dependencia->id : null,
                'activo' => $rolObject->pivot->activo
            ];
        } else {
            $roles = $user->roles()->whereIn('roles.id', $ids)->get();
            $rolesProcesed = [];

            function crearResRol($params)
            {
                $nombre = $params['rol']->nombre;
                $exist = [
                    'estudiante' => isset($params['estudiante']),
                    'dependencia' => isset($params['dependencia']),
                ];

                if ($exist['estudiante']) $nombre .= ' - ' . $params['estudiante']->estudio->programa->nombre;
                if ($exist['dependencia']) $nombre .= ' - ' . $params['dependencia']->nombre;

                return [
                    'id' => $params['rol']->id,
                    'nombre' => $nombre,
                    'estudiante_id' => $exist['estudiante'] ? $params['estudiante']->id : null,
                    'dependencia_id' => $exist['dependencia'] ? $params['dependencia']->id : null,
                    'activo' => $params['rol']->pivot->activo
                ];
            }

            foreach ($roles as $rol) {

                $params = [
                    'rol' => $rol
                ];

                switch ($rol->id) {
                    case $rolesPlatforma['estudiante']->id:
                        foreach ($user->persona->estudiantes as $e) {
                            $params['estudiante'] = $e;
                            array_push($rolesProcesed, crearResRol($params));
                        }
                        break;
                    case $rolesPlatforma['dependencia']->id:
                        foreach ($user->dependencias as $dep) {
                            $params['dependencia'] = $dep;
                            array_push($rolesProcesed, crearResRol($params));
                        }
                        break;
                    default:
                        array_push($rolesProcesed, crearResRol($params));
                        break;
                }
            }

            if (count($rolesProcesed) === 0) return response('Usuario no autorizado para entrar', 400);
            if (count($rolesProcesed) > 1) return $rolesProcesed;

            $rol = $rolesProcesed[0];
        }

        if ($rol['activo'] !== '1') return response('Rol no activo', 400);

        $authRes = $client->request('POST', env('url_auth') . 'authic/auth', [
            'form_params' => [
                'user' => $req->username,
                'password' => $req->password,
                'token' => strtoupper(md5('@7t3nt1c4c10n' . \Carbon\Carbon::now()->toDateString()))
            ]
        ])->getBody();

        if ($authRes == 'false') return response('Uusario o contraseña incorrectos', 400);

        $ur = UsuarioRol::where('usuario_id', $user->id)
            ->where('rol_id', $rol['id'])
            ->first();

        Auth::login($user);
        session(['ur' => $ur]);
        session(['estudiante_id' => $rol['estudiante_id']]);
        session(['dependencia_id' => $rol['dependencia_id']]);

        return 'ok';
    }

    public function sessionData()
    {
        $data = [];
        $check = Auth::check();

        if (session('ur')) {
            $ur = UsuarioRol::find(session('ur')->id);
            $data = [
                'nombre' => $ur->persona->nombre,
                'id' => $ur->id,
                'rol' => $ur->rol->nombre
            ];
        }

        return compact('check', 'data');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();

        return redirect('/');
    }
}
