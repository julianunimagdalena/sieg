<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FechaGrado extends Model
{
    protected $table = 'fechas_de_grado';
    protected $visible = ['id', 'nombre', 'anio', 'fecha_grado'];

    public function tipoGrado()
    {
        return $this->belongsTo('App\Models\TipoGrado', 'tipo_grado');
    }

    public function getDescripcionAttribute()
    {
        return $this->nombre . ' - ' . $this->tipoGrado->nombre;
    }

    public function getFechaFormatedAttribute()
    {
        $fecha = Carbon::parse($this->fecha_grado);
        return $fecha->format('d/m/Y');
    }
}
