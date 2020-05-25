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

    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', 'estudiante_documento', 'idEstudiante', 'idDocumento');
    }

    public function getEstadoDocumentosAttribute()
    {
        $estado = null;
        $estados = Variables::estados();
        $ndocTotal =  $this->estudianteDocumento()->count();
        $ndocRechazados = $this->estudianteDocumento()->where('estado_id', $estados['rechazado']->id)->count();
        $ndocAprobados = $this->estudianteDocumento()->where('estado_id', $estados['aprobado']->id)->count();

        if ($ndocRechazados > 0) $estado = $estados['rechazado']->nombre;
        else if ($ndocAprobados === $ndocTotal) $estado = $estados['aprobado']->nombre;
        else $estado = $estados['pendiente']->nombre;

        return $estado;
    }
}
