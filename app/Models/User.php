<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = true;
    protected $table = 'usuarios';
    // protected $visible = ['identificacion'];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function usuarioRol()
    {
        return $this->hasMany('App\Models\UsuarioRol', 'usuario_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Rol', 'usuario_rol', 'usuario_id', 'rol_id')->withPivot('activo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona', 'idPersona');
    }

    public function dependenciasModalidades()
    {
        return $this->belongsToMany('App\Models\DependenciaModalidad', 'coordinador_programas', 'idCoordinador', 'idPrograma');
    }
}
