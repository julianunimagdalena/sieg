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

    public function getCanGenerarAttribute()
    {
        $estados = Variables::estados();
        $canGenerar = Variables::documentosCanGenerar();

        return in_array($this->idDocumento, $canGenerar) && $this->estado_id !== $estados['aprobado']->id;
    }

    public function getCanShowAttribute()
    {
        $estados = Variables::estados();
        return in_array($this->estado_id, [$estados['aprobado']->id, $estados['pendiente']->id]);
    }

    public function getCanAprobarAttribute()
    {
        $estados = Variables::estados();
        $can = Variables::documentosCanCambiarEstado();

        return in_array($this->idDocumento, $can) && !in_array($this->estado_id, [$estados['aprobado']->id, $estados['sin_cargar']->id]);
    }

    public function getCanRechazarAttribute()
    {
        $estados = Variables::estados();
        $can = Variables::documentosCanCambiarEstado();

        return in_array($this->idDocumento, $can) && !in_array($this->estado_id, [$estados['rechazado']->id, $estados['sin_cargar']->id]);
    }

    public function getCanCargarDireccionAttribute()
    {
        $can = Variables::documentosCanCargarDireccion();
        return in_array($this->idDocumento, $can);
    }
}
