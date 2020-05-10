<?php

namespace App\Http\Middleware;

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
        $roles = explode('|', $params);
        $isValid = false;

        foreach ($roles as $rol) {
            if ($rol === session('ur')->rol->nombre) $isValid = true;
        }

        if ($isValid) return $next($request);
        else return response('No autorizado', 401);
    }
}
