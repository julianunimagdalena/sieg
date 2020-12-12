<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class EstudiantePazSalvo extends Model
{
    public $timestamps = false;
    protected $table = 'est_pazsalvo';
    protected $casts = [
        'paz_salvo' => 'boolean'
    ];

    public function pazSalvo()
    {
        return $this->belongsTo('App\Models\PazSalvo', 'idPazsalvo');
    }
}
