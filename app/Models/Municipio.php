<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    public $timestamps = false;
    protected $table = 'municipios';
    protected $casts = [
        'idDepartamento' => 'integer'
    ];

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento', 'idDepartamento');
    }
}
