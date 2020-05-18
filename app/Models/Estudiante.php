<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    public function pazSalvos()
    {
        return $this->belongsToMany('App\Models\PazSalvo', 'est_pazsalvo', 'idEstudiante', 'idPazsalvo');
    }

    public function estudio()
    {
        return $this->belongsTo('App\Models\DependenciaModalidad', 'idPrograma');
    }

    public function procesoGrado()
    {
        return $this->hasOne('App\Models\ProcesoGrado', 'idEstudiante');
    }
}
