<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechaGrado extends Model
{
    protected $table = 'fechas_de_grado';
    protected $visible = ['id', 'nombre', 'anio', 'fecha_grado'];
}
