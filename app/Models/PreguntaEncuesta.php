<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaEncuesta extends Model
{
    public $timestamps = false;
    protected $table = 'pregunta_encuesta';
    protected $casts = [
        'modulo_id' => 'integer',
        'pregunta_root_id' => 'integer',
        'orden' => 'integer',
        'obligatorio' => 'boolean',
        'abierta' => 'boolean',
    ];

    public function scopeNoroot($query)
    {
        return $query->whereNull('pregunta_root_id');
    }

    public function posiblesRespuestas()
    {
        return $this->hasMany('App\Models\PosibleRespuestaEncuesta', 'pregunta_id');
    }
}
