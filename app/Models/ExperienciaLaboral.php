<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienciaLaboral extends Model
{
    public $timestamps = false;
    protected $table = 'experiencias_laborales';
    protected $casts = [
        'duracion' => 'integer',
        'idHoja' => 'integer',
        'nivel_cargo_id' => 'integer',
        'tipo_vinculacion_id' => 'integer',
        'municipio_id' => 'integer',
        'salario_id' => 'integer',
    ];

    public function nivelCargo()
    {
        return $this->belongsTo('App\Models\NivelCargo', 'nivel_cargo_id');
    }

    public function salario()
    {
        return $this->belongsTo('App\Models\Salario', 'salario_id');
    }

    public function tiempo()
    {
        return $this->belongsTo('App\Models\Duracion', 'duracion');
    }

    public function tipoVinculacion()
    {
        return $this->belongsTo('App\Models\TipoVinculacion', 'tipo_vinculacion_id');
    }

    public function municipio()
    {
        return $this->belongsTo('App\Models\Municipio', 'municipio_id');
    }
}
