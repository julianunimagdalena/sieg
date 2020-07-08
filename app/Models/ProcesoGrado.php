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
        'tipo_vinculacion_tutor_id' => 'integer',
        'no_aprobado' => 'boolean',
        'mejor_ecaes' => 'boolean',
        'mencion_honor' => 'boolean',
        'incentivo_nacional' => 'boolean',
        'incentivo_institucional' => 'boolean'
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

    public function tipoVinculacionTutor()
    {
        return $this->belongsTo('App\Models\TipoVinculacion', 'tipo_vinculacion_tutor_id');
    }
}
