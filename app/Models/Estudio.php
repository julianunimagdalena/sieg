<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    public $timestamps = false;
    protected $table = 'estudiosrealizados';
    protected $casts = [
        'idHoja' => 'integer',
        'idMunicipio' => 'integer',
        'anioGrado' => 'integer',
        'graduado' => 'boolean'
    ];
}
