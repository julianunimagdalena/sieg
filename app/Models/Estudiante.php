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
}
