<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class EstudianteDocumento extends Model
{
    protected $table = 'estudiante_documento';

    public function documento()
    {
        return $this->belongsTo('App\Models\Documento', 'idDocumento');
    }

    public function estado()
    {
        return $this->belongsTo('App\Models\Estado', 'estado_id');
    }

    public function estudiante()
    {
        return $this->belongsTo('App\Models\Estudiante', 'idEstudiante');
    }

    public function getFilenameAttribute()
    {
        return $this->documento->abrv . '_' . $this->estudiante->codigo . '.pdf';
    }

    public function getPathAttribute()
    {
        return Variables::$carpetaDocumentosEstudiantes . $this->id . '/' . $this->filename;
    }
}
