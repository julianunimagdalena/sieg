<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudGrado extends Model
{
    protected $table = 'solicitud_grado';
    protected $hidden = ['programa_id', 'created_at', 'updated_at', 'estado_id', 'fecha_realizacion', 'motivo_rechazo'];

    public function fechaGrado()
    {
        return $this->belongsTo('App\Models\FechaGrado', 'fecha_grado_id');
    }
}
