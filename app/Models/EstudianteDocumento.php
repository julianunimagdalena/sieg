<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class EstudianteDocumento extends Model
{
    protected $table = 'estudiante_documento';
    protected $casts = [
        'idDocumento' => 'integer',
        'estado_id' => 'integer'
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

    public function scopeType($query, $key)
    {
        $documentos = Variables::documentos();
        $query->where('idDocumento', $documentos[$key]->id);
    }

    public function scopeEstado($query, $key)
    {
        $estados = Variables::estados();
        $query->where('estado_id', $estados[$key]->id);
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
        $can = false;
        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);

        if ($ur->rol_id === $roles['coordinador']->id) {
            $canGenerar = Variables::documentosCanGenerar();
            $documentos = Variables::documentos();
            $can = in_array($this->idDocumento, $canGenerar);

            if ($this->idDocumento === $documentos['ficha']->id) {
                $pg = $this->estudiante->procesoGrado;
                $can = $can && ($pg->estado_ficha && $pg->estado_encuesta);
            }

            if ($this->idDocumento === $documentos['paz_salvos']->id) {
                $psPendientes = $this->estudiante->estudiantePazSalvo()->where('paz_salvo', false)->count();
                $can = $can && !$psPendientes;
            }
        }

        return $can;
    }

    public function getCanShowAttribute()
    {
        $estados = Variables::estados();
        return $this->estado_id !== $estados['sin_cargar']->id;
    }

    public function getCanAprobarAttribute()
    {
        $b = false;
        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);

        if ($ur->rol_id === $roles['coordinador']->id) {
            $estados = Variables::estados();
            $can = Variables::documentosCanCambiarEstado();
            $b = in_array($this->idDocumento, $can) && !in_array($this->estado_id, [$estados['aprobado']->id, $estados['sin_cargar']->id]);
        }

        return $b;
    }

    public function getCanRechazarAttribute()
    {
        $roles = Variables::roles();
        $estados = Variables::estados();
        $ur = UsuarioRol::find(session('ur')->id);
        $b = !in_array($this->estado_id, [$estados['rechazado']->id, $estados['sin_cargar']->id]);

        switch ($ur->rol_id) {
            case $roles['coordinador']->id:
                $can = Variables::documentosCanCambiarEstado();
                $b = $b && in_array($this->idDocumento, $can);
                break;

            case $roles['administrador']->id:
                $b = false;
                break;
        }

        return $b;
    }

    public function getCanCargarDireccionAttribute()
    {
        $b = false;
        $roles = Variables::roles();
        $ur = UsuarioRol::find(session('ur')->id);

        if ($ur->rol_id === $roles['coordinador']->id) {
            $can = Variables::documentosCanCargarDireccion();
            $doc_ids = array_values(array_map(fn ($doc) => $doc->id, Variables::documentos()));
            $b = in_array($this->idDocumento, $can) || !in_array($this->idDocumento, $doc_ids);
        }

        return $b;
    }

    public function getCanCargarEstudianteAttribute()
    {
        $estados = Variables::Estados();
        $can = Variables::documentosCanCargarEstudiante();

        return in_array($this->idDocumento, $can);
        // return in_array($this->idDocumento, $can) && $this->estado_id !== $estados['aprobado']->id;
    }
}
