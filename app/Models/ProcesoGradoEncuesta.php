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
}
