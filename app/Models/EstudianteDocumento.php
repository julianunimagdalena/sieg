<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class EstudianteDocumento extends Model
{
    protected $table = 'estudiante_documento';
    protected $casts = [
        'idDocumento' => 'integer'
    ];

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

    public function getFolderAttribute()
    {
        $estudiante = $this->estudiante;
        $fecha = $estudiante->procesoGrado->fechaGrado;
        $dependencia = $estudiante->estudio->programa;
        $folder = Variables::$carpetaDocumentosEstudiantes
            . $fecha->id
            . '/' . $dependencia->id
            . '/' . $estudiante->id;

        return $folder;
    }

    public function getPathAttribute()
    {
        $path = $this->folder . '/' . $this->filename;
        return $path;
    }

    public function getCanGenerarAttribute()
    {
        $canGenerar = Variables::documentosCanGenerar();
        $documentos = Variables::documentos();
        $can = in_array($this->idDocumento, $canGenerar);

        if ($this->idDocumento === $documentos['ficha']->id) {
            $pg = $this->estudiante->procesoGrado;
            $can = $can && ($pg->estado_ficha && $pg->estado_encuesta);
        }

        return $can;
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
