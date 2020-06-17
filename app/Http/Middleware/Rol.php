<?php

namespace App\Http\Middleware;

use App\Models\UsuarioRol;
use Closure;

class Rol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $params)
    {
        $isValid = false;
        $roles = explode('|', $params);
        $ur = UsuarioRol::find(session('ur')->id);

        if ($request->ip() !== env('LOCAL_IP')) {
            foreach ($roles as $rol) {
                if ($rol === $ur->rol->nombre) $isValid = true;
            }
        } else $isValid = true;


        if ($isValid) return $next($request);
        else return response('No autorizado', 401);
    }
}
