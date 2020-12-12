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

    public function dependencias()
    {
        return $this->belongsToMany('App\Models\Dependencia', 'usuario_dependencia', 'usuario_id', 'dependencia_id');
    }

    public function getProgramasCoordinadosAttribute()
    {
        $programa_ids = [];

        foreach ($this->dependenciasModalidades as $dm) {
            if (!in_array($dm->idPrograma, $programa_ids)) array_push($programa_ids, $dm->idPrograma);
        }

        $programas = array_map(fn ($id) => Dependencia::select('id', 'nombre')->find($id), $programa_ids);
        return $programas;
    }

    public function getEstudiantesCoordinadosAttribute()
    {
        $dm_ids = $this->dependenciasModalidades->pluck('id');
        return Estudiante::whereIn('idPrograma', $dm_ids);
    }
}
