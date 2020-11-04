<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaEncuesta extends Model
{
    public $timestamps = true;
    protected $table = 'respuesta_encuesta';
    protected $casts = [
        'pregunta_id' => 'integer',
        'pge_id' => 'integer',
    ];
}
