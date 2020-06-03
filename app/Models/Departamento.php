<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public $timestamps = false;
    protected $table = 'departamentos';
    protected $casts = [
        'idPais' => 'integer'
    ];

    public function pais()
    {
        return $this->belongsTo('App\Models\Pais', 'idPais');
    }
}
