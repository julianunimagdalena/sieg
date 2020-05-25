<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaVida extends Model
{
    public $timestamps = false;
    protected $table = 'hojavida';
    protected $casts = [
        'idPersona' => 'integer',
        'activa' => 'boolean',
        'laborando' => 'boolean',
    ];

    public function estudios()
    {
        return $this->hasMany('App\Models\Estudio', 'idHoja');
    }

    public function distinciones()
    {
        return $this->hasMany('App\Models\Distincion', 'idHoja');
    }

    public function asociaciones()
    {
        return $this->hasMany('App\Models\Asociacion', 'idHoja');
    }

    public function concejos()
    {
        return $this->belongsToMany('App\Models\Concejo', 'hojavida_concejos', 'idHoja', 'idConcejo');
    }

    public function discapacidades()
    {
        return $this->belongsToMany('App\Models\Discapacidad', 'hoja_discapacidades', 'idHoja', 'idDiscapacidad');
    }

    public function idiomas()
    {
        return $this->hasMany('App\Models\HojaVidaIdioma', 'idHoja');
    }

    public function experiencias()
    {
        return $this->hasMany('App\Models\ExperienciaLaboral', 'idHoja');
    }
}
