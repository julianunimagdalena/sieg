<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HojaVidaIdioma extends Model
{
    public $timestamps = false;
    protected $table = 'hojadevida_idiomas';
    protected $casts = [
        'idHoja' => 'integer',
        'idIdioma' => 'integer',
        'lectura' => 'integer',
        'escritura' => 'integer',
        'habla' => 'integer'
    ];

    public function nivelLectura()
    {
        return $this->belongsTo('App\Models\NivelIdioma', 'lectura');
    }

    public function nivelEscritura()
    {
        return $this->belongsTo('App\Models\NivelIdioma', 'escritura');
    }

    public function nivelHabla()
    {
        return $this->belongsTo('App\Models\NivelIdioma', 'habla');
    }

    public function idioma()
    {
        return $this->belongsTo('App\Models\Idioma', 'idIdioma');
    }
}
