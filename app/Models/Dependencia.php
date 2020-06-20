<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    public $timestamps = false;
    protected $table = 'dependencias';
    protected $casts = [
        'idTipo' => 'integer',
        'idNivelestudio' => 'integer'
    ];

    public function nivelEstudio()
    {
        return $this->belongsTo('App\Models\NivelEstudio', 'idNivelestudio');
    }

    public function getNombreUcwordsAttribute()
    {
        $nombre = strtolower($this->nombre);
        return ucwords($nombre);
    }
}
