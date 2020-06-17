<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    public function estudiantePazSalvo()
    {
        return $this->hasMany('App\Models\EstudiantePazSalvo', 'idEstudiante');
    }

    public function pazSalvos()
    {
        return $this->belongsToMany('App\Models\PazSalvo', 'est_pazsalvo', 'idEstudiante', 'idPazsalvo');
    }

    public function estudio()
    {
        return $this->belongsTo('App\Models\DependenciaModalidad', 'idPrograma');
    }

    public function procesoGrado()
    {
        return $this->hasOne('App\Models\ProcesoGrado', 'idEstudiante');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona', 'idPersona');
    }

    public function estudianteDocumento()
    {
        return $this->hasMany('App\Models\EstudianteDocumento', 'idEstudiante');
    }

    public function getDocumentosEstudianteAttribute()
    {
        $documentos = Variables::documentos();
        $valid = [
            $documentos['ecaes']->id
        ];

        return $this->estudianteDocumento()->whereIn('idDocumento', $valid);
    }

    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', 'estudiante_documento', 'idEstudiante', 'idDocumento');
    }

    public function dependenciaModalidad()
    {
        return $this->belongsTo('App\Models\DependenciaModalidad', 'idPrograma');
    }

    public function getEstadoDocumentosAttribute()
    {
        $estado = null;
        $estados = Variables::estados();
        $ndocTotal =  $this->documentos_estudiante->count();
        $ndocRechazados = $this->documentos_estudiante->where('estado_id', $estados['rechazado']->id)->count();
        $ndocCargados = $this->documentos_estudiante
            ->whereIn('estado_id', [$estados['aprobado']->id, $estados['pendiente']->id])
            ->count();

        if ($ndocRechazados > 0) $estado = $estados['rechazado']->nombre;
        else if ($ndocCargados === $ndocTotal) $estado = $estados['aprobado']->nombre;
        else $estado = $estados['pendiente']->nombre;

        return $estado;
    }

    public function getCanAprobarAttribute()
    {
        $can = false;
        $roles = Variables::roles();
        $estados = Variables::estados();
        $pg = $this->procesoGrado;

        switch (session('ur')->rol_id) {
            case $roles['coordinador']->id:
                $count = $this->estudianteDocumento()->where('estado_id', '<>', $estados['aprobado']->id)->count();
                $can = $count === 0 &&
                    $pg->resultado_ecaes &&
                    $pg->titulo_memoria_grado &&
                    $pg->codigo_ecaes;

                break;

            case $roles['secretariaGeneral']->id:
                $can = $pg->estado_programa_id === $estados['aprobado']->id;
                break;
        }

        return $can;
    }

    public function getEstadoAttribute()
    {
        $pg = $this->procesoGrado;
        if ($pg->no_aprobado) return Variables::$estadoNoAprobado;

        $estados = Variables::estados();
        $isOk = $pg->estado_encuesta
            && $pg->estado_ficha
            && $this->estado_documentos === $estados['aprobado']->nombre
            && $pg->confirmacion_asistencia !== null;

        return $isOk ? $estados['aprobado']->nombre : $estados['pendiente']->nombre;
    }

    public function scopeGraduados($query)
    {
        $tipos = Variables::tiposEstudiante();
        return $query->where('idTipo', $tipos['graduado']->id);
    }
}
