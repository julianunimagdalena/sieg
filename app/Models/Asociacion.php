<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asociacion extends Model
{
    public $timestamps = false;
    protected $table = 'asociaciones';
    protected $casts = [
        'idHoja' => 'integer'
    ];
}
