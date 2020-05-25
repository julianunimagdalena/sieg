<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoGrado extends Model
{
    protected $table = 'proceso_grado';
    protected $casts = [
        'estado_ficha' => 'boolean',
        'estado_encuesta' => 'boolean',
        'estado_secretaria' => 'boolean',
        'estado_programa' => 'boolean',
        'confirmacion_asistencia' => 'boolean',
    ];
}
