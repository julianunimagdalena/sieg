<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concejo extends Model
{
    public $timestamps = false;
    protected $table = 'concejos_profesionales';
    protected $casts = [
        'idHoja' => 'integer'
    ];
}
