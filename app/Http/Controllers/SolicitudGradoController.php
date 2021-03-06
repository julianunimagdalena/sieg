<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitudGradoRequest;
use App\Models\DependenciaModalidad;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\SolicitudGrado;
use App\Tools\StringManager;
use App\Tools\Variables;
use App\Tools\WSAdmisiones;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SolicitudGradoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'pendientes'
        ]]);

        $roles = Variables::roles();
        $this->middleware('rol:' . $roles['coordinador']->nombre, ['only' => [
            'pendientes'
        ]]);
    }

    public function programasPorIdentificacion(string $identificacion)
    {
        $programas = [];
        $ws = new WSAdmisiones();
        $data = $ws->getInformacionGraduadoByDocumentoIdentidad($identificacion);

        foreach ($data as $d) {
            if ($d->situacionAcademica === 'REGULAR')
                array_push($programas, $d->nombreDelPrograma);
        }

        return $programas;
    }

    public function solicitar(SolicitudGradoRequest $req)
    {
        $estados = Variables::estados();
        $ws = new WSAdmisiones();
        $data = $ws->getInformacionGraduadoByDocumentoIdentidad($req->identificacion);
        $datarow = array_values(array_filter($data, function ($d) use ($req) {
            return $d->nombreDelPrograma === $req->programa;
        }))[0];

        if (!$datarow) return response('', 400);

        $jornada = substr($datarow->codigoPrograma, -1);
        $programa = StringManager::eliminarTildes($datarow->nombreDelPrograma);
        $dm = DependenciaModalidad::whereHas('programa', fn ($pro) => $pro->where('nombre', $programa))
            ->whereHas('modalidad', fn ($mod) => $mod->where('nombre', $datarow->modalidad))
            ->whereHas('jornada', fn ($jor) => $jor->where('nombre', $jornada))
            ->first();

        if (!$dm) return response('No se encuentra la dependencia correspondiente, favor notificar e intentar más tarde', 400);

        $estados = Variables::estados();
        $solicitud = SolicitudGrado::where('identificacion_estudiante', $datarow->numeroDocumento)
            ->where('programa_id', $dm->id)
            ->where('estado_id', $estados['pendiente']->id)
            ->first();
        if ($solicitud) return response('Ya hay una solicitud para este programa', 400);

        $persona = Persona::where('identificacion', $datarow->numeroDocumento)->first();

        if ($persona) {
            $tipos = Variables::tiposEstudiante();
            $estudiante = $persona->estudiantes()->where('idPrograma', $dm->id)->first();

            if ($estudiante) {
                if ($estudiante->idTipo === $tipos['graduado']->id) return response('Estudiante ya graduado', 400);
                if (!$estudiante->procesoGrado->no_aprobado)
                    return response('Ya se encuentra registrado en el sistema con este programa', 400);
            }
        }

        $solicitud = new SolicitudGrado();
        $solicitud->fecha = Carbon::now();
        $solicitud->identificacion_estudiante = $datarow->numeroDocumento;
        $solicitud->nombre_estudiante = $datarow->nombres . ' ' . $datarow->apellidos;
        $solicitud->codigo_estudiante = $datarow->codigoEstudiantil;
        $solicitud->fecha_grado_id = $req->fecha_id;
        $solicitud->programa_id = $dm->id;
        $solicitud->estado_id = $estados['pendiente']->id;

        $solicitud->save();
        return 'ok';
    }

    public function getPendientes()
    {
        $estados = Variables::estados();
        $programaIds = session('ur')->usuario->dependenciasModalidades->pluck('id');
        $solicitudes = SolicitudGrado::where('estado_id', $estados['pendiente']->id)
            ->whereIn('programa_id', $programaIds);

        return $solicitudes;
    }

    public function pendientes()
    {
        $solicitudes = $this->getPendientes()->with('fechaGrado')->orderBy('fecha', 'desc')->get();
        return $solicitudes;
    }

    public function eliminar(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|required|exists:solicitud_grado,id',
            'motivo' => 'required',
        ], ['*.required' => 'Obligatorio']);

        $estados = Variables::estados();
        $solicitud = SolicitudGrado::find($request->id);
        $solicitud->estado_id = $estados['rechazado']->id;
        $solicitud->motivo_rechazo = $request->motivo;
        $solicitud->save();

        return 'ok';
    }


    public function getNumeroSolicitudes()
    {
        return session('ur')->solicitudes_grado_pendientes;
    }
}
