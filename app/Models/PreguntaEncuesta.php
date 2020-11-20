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
        'obligatorio' => 'boolean',
        'abierta' => 'boolean',
        'multiple' => 'boolean',
    ];

    public function scopeNoroot($query)
    {
        return $query->whereNull('pregunta_root_id');
    }

    public function posiblesRespuestas()
    {
        return $this->hasMany('App\Models\PosibleRespuestaEncuesta', 'pregunta_id');
    }

    public function subpreguntas()
    {
        return $this->hasMany('App\Models\PreguntaEncuesta', 'pregunta_root_id');
    }
}
