<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloEncuesta extends Model
{
    public $timestamps = false;
    protected $table = 'modulo_encuesta';
    protected $casts = [
        'encuesta_id' => 'integer'
    ];

    public function preguntas()
    {
        return $this->hasMany('App\Models\PreguntaEncuesta', 'modulo_id');
    }
}
