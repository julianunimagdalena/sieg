<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class DependenciaDocumento extends Model
{
    protected $table = 'dependencia_documento';
    protected $casts = [
        'dependencia_id' => 'integer',
        'documento_id' => 'integer',
    ];

    public function dependencia()
    {
        return $this->belongsTo('App\Models\Dependencia', 'dependencia_id');
    }

    public function documento()
    {
        return $this->belongsTo('App\Models\Documento', 'documento_id');
    }

    public function getCanDeleteAttribute()
    {
        $documentos = Variables::documentos();
        $whitelist = [
            $documentos['ecaes']->id,
            $documentos['titulo_grado']->id,
        ];

        return !in_array($this->documento_id, $whitelist);
    }
}
