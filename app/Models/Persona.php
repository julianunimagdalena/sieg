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

    public function getProgresoFichaAttribute()
    {
        $progreso = 0;
        $hoja = $this->hojaVida;

        if ($this->idEstadoCivil) $progreso += 2;
        if ($this->estrato) $progreso += 2;
        if ($this->fecha_expedicion) $progreso += 2;
        if ($this->ciudadResidencia) $progreso += 2;
        if ($this->celular) $progreso += 2;
        if ($this->direccion) $progreso += 2;
        if ($this->sector) $progreso += 2;
        if ($this->correo) $progreso += 2;
        if ($hoja->perfil) $progreso += 14;
        if ($hoja->asociaciones()->count() > 0) $progreso += 14;
        if ($hoja->concejos()->count() > 0) $progreso += 14;
        if ($hoja->distinciones()->count() > 0) $progreso += 14;
        if ($hoja->discapacidades()->count() > 0) $progreso += 14;
        if ($hoja->laborando !== null) $progreso += 14;

        return $progreso;
    }
}
