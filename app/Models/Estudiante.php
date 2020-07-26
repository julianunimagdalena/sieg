<?php

namespace App\Models;

use App\Tools\Variables;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';
    protected $casts = [
        'idTipo' => 'integer',
        'distincion' => 'integer',
    ];

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
                $programa = $this->estudio->programa;
                $doc_count = $this->estudianteDocumento()->where('estado_id', '<>', $estados['aprobado']->id)->count();
                $ps_count = $this->estudiantePazSalvo()->where('paz_salvo', false)->count();
                $can = $doc_count === 0
                    && $ps_count === 0;

                if ($programa->carga_ecaes) {
                    $can = $can
                        && ($pg->titulo_grado && trim($pg->titulo_grado) !== '-')
                        && ($pg->modalidad_grado && trim($pg->modalidad_grado) !== '-')
                        && ($pg->titulo_memoria_grado && trim($pg->titulo_memoria_grado) !== '-')
                        && ($pg->nota && trim($pg->nota) !== '-')
                        && $pg->tutor_grado !== null
                        && $pg->tipo_vinculacion_tutor_id !== null;
                }

                if ($programa->carga_titulo_grado) {
                    $can = $can
                        && $pg->codigo_ecaes !== null
                        && $pg->mejor_ecaes !== null
                        && $pg->incentivo_nacional !== null
                        && $pg->inventivo_institucional !== null;
                }

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

    public function getCargaEcaesAttribute()
    {
        $res = true;
        $niveles = Variables::nivelesEstudio();

        if ($this->estudio->programa->idNivelestudio === $niveles['tecnico_laboral']->id) $res = false;

        return $res;
    }

    public function getCargaTituloGradoAttribute()
    {
        $res = true;
        $niveles = Variables::nivelesEstudio();

        if ($this->estudio->programa->idNivelestudio === $niveles['tecnico_laboral']->id) $res = false;

        return $res;
    }

    public function getDocumentosInicialesAttribute()
    {
        $docs = [];
        $estados = Variables::estados();
        $programa = $this->estudio->programa;

        foreach ($programa->documentosNecesarios as $doc) {
            $docs[$doc->id] = ['estado_id' => $estados['sin_cargar']->id];
        }

        return $docs;
    }

    public function getPazSalvosInicialesAttribute()
    {
        $programa = $this->estudio->programa;
        $ps = $programa->pazSalvosNecesarios->pluck('id');

        return $ps;
    }
}
