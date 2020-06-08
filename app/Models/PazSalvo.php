<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PazSalvo extends Model
{
    public $timestamp = false;
    protected $table = 'paz_salvos';

    public function dependencia()
    {
        return $this->belongsTo('App\Models\Dependencia', 'idDependencia');
    }

    public function getNombreUcwordsAttribute()
    {
        $nombre = strtolower($this->nombre);
        return ucwords($nombre);
    }
}
