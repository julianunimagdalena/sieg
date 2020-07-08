<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    public $timestamps = false;
    protected $table = 'dependencias';
    protected $casts = [
        'idTipo' => 'integer',
        'idNivelestudio' => 'integer',
        'carga_ecaes' => 'boolean',
        'carga_titulo_grado' => 'boolean'
    ];

    public function nivelEstudio()
    {
        return $this->belongsTo('App\Models\NivelEstudio', 'idNivelestudio');
    }

    public function dependenciaPazSalvo()
    {
        return $this->hasMany('App\Models\DependenciaPazSalvo', 'dependencia_id');
    }

    public function getNombreUcwordsAttribute()
    {
        $nombre = strtolower($this->nombre);
        return ucwords($nombre);
    }

    public function pazSalvosNecesarios()
    {
        return $this->belongsToMany('App\Models\PazSalvo', 'dependencia_paz_salvo', 'dependencia_id', 'paz_salvo_id');
    }
}
