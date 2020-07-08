<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DependenciaPazSalvo extends Model
{
    protected $table = 'dependencia_paz_salvo';

    public function pazSalvo()
    {
        return $this->belongsTo('App\Models\PazSalvo', 'paz_salvo_id');
    }

    public function dependencia()
    {
        return $this->belongsTo('App\Models\Dependencia', 'dependencia_id');
    }
}
