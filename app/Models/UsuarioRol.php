<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    public $timestamps = false;
    protected $table = 'usuario_rol';

    protected $visible = ['id', 'usuario_id', 'rol_id', 'activo'];
    protected $casts = [
        'activo' => 'boolean',
        'usuario_id' => 'integer',
        'rol_id' => 'integer'
    ];

    public function rol()
    {
        return $this->belongsTo('App\Models\Rol', 'rol_id');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User', 'usuario_id');
    }

    public function getPersonaAttribute()
    {
        return $this->usuario->persona;
    }
}
