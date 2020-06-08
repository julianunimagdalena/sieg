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
        'idEstudiante' => 'integer',
        'no_aprobado' => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo('App\Models\Estudiante', 'idEstudiante');
    }

    public function fechaGrado()
    {
        return $this->belongsTo('App\Models\FechaGrado', 'idFecha');
    }

    public function estadoPrograma()
    {
        return $this->belongsTo('App\Models\Estado', 'estado_programa_id');
    }

    public function estadoSecretaria()
    {
        return $this->belongsTo('App\Models\Estado', 'estado_secretaria_id');
    }
}
