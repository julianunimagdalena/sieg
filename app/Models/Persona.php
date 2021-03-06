<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getNombreInvertidoAttribute()
    {
        return $this->apellidos . ' ' . $this->nombres;
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

    public function tipoDocumento()
    {
        return $this->belongsTo('App\Models\TipoDocumento', 'tipodoc');
    }

    public function genero()
    {
        return $this->belongsTo('App\Models\Genero', 'idGenero');
    }

    public function estadoCivil()
    {
        return $this->belongsTo('App\Models\EstadoCivil', 'idEstadoCivil');
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

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = strtoupper($value);
    }

    public function setApellidosAttribute($value)
    {
        $this->attributes['apellidos'] = strtoupper($value);
    }

    public function getLugarNacimientoAttribute()
    {
        $municipio = $this->municipioNacimiento;
        $departamento = $municipio->departamento;

        return $municipio->nombre . ' - ' . $departamento->nombre . ', ' . $departamento->pais->nombre;
    }

    public function getFechaNacimientoFormatedAttribute()
    {
        $nacimiento = Carbon::parse($this->fechaNacimiento);
        return $nacimiento->format('d/m/Y');
    }

    public function getEdadAttribute()
    {
        $nacimiento = Carbon::parse($this->fechaNacimiento);
        $now = Carbon::now();

        return $now->diffInYears($nacimiento);
    }
}
