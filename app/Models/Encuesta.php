<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    public $timestamps = false;
    protected $table = 'encuesta';

    public function modulos()
    {
        return $this->hasMany('App\Models\ModuloEncuesta', 'encuesta_id');
    }
}
