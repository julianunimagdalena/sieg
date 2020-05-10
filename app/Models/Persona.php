<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    public $timestamps = true;
    protected $table = 'personas';

    public function getNombreAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function usuario()
    {
        return $this->hasOne('App\Models\User', 'idPersona');
    }
}
