<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosibleRespuestaEncuesta extends Model
{
    public $timestamps = false;
    protected $table = 'posible_respuesta_encuesta';
    protected $casts = [
        'pregunta_id' => 'integer',
        'to_pregunta_id' => 'integer',
        'abierta' => 'boolean',
    ];
}
