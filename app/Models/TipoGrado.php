<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGrado extends Model
{
    public $timestamps = false;
    protected $table = 'tipos_de_grados';

    public function getNombreAttribute($value)
    {
        return strtoupper($value);
    }
}
