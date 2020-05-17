<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    public $timestamps = true;
    protected $table = 'personas';
    protected $casts = [
        'ciudadOrigen' => 'integer',
        'ciudadResidencia' => 'integer',
        'idGenero' => 'integer',
        'tipodoc' => 'integer',
        'idEstadoCivil' => 'integer',
    ];

    public function getNombreAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function usuario()
    {
        return $this->hasOne('App\Models\User', 'idPersona');
    }

    public function municipioNacimiento()
    {
        return $this->belongsTo('App\Models\Municipio', 'ciudadOrigen');
    }

    public function municipioResidencia()
    {
        return $this->belongsTo('App\Models\Municipio', 'ciudadResidencia');
    }

    public function estudiantes()
    {
        return $this->hasMany('App\Models\Estudiante', 'idPersona');
    }

    public function hojaVida()
    {
        return $this->hasOne('App\Models\HojaVida', 'idPersona');
    }
}
