<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DependenciaModalidad extends Model
{
    public $timestamps = false;
    protected $table = 'dependencias_modalidades';

    public function programa()
    {
        return $this->belongsTo('App\Models\Dependencia', 'idPrograma');
    }

    public function facultad()
    {
        return $this->belongsTo('App\Models\Dependencia', 'idFacultad');
    }

    public function jornada()
    {
        return $this->belongsTo('App\Models\Jornada', 'idJornada');
    }

    public function modalidad()
    {
        return $this->belongsTo('App\Models\ModalidadEstudio', 'idModalidad');
    }
}
