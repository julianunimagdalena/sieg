<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoGradoEncuesta extends Model
{
    public $timestamps = false;
    protected $table = 'proceso_grado_encuesta';
    protected $casts = [
        'proceso_grado_id' => 'integer',
        'encuesta_id' => 'integer',
    ];

    public function procesoGrado()
    {
        return $this->belongsTo('App\Models\ProcesoGrado', 'proceso_grado_id');
    }

    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaEncuesta', 'pge_id');
    }
}
