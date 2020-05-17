<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distincion extends Model
{
    public $timestamps = false;
    protected $table = 'distinciones';
    protected $casts = [
        'idHoja' => 'integer'
    ];
}
