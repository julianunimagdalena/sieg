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
        $datarow = array_filter($data, function ($d) use ($req) {
            return $d->nombreDelPrograma === $req->programa;
        })[0];

        if (!$datarow) return response('', 400);

        $jornada = substr($datarow->codigoPrograma, -1);
        $programa = StringManager::eliminarTildes($datarow->nombreDelPrograma);
        $dm = DependenciaModalidad::whereHas('programa', fn ($pro) => $pro->where('nombre', $programa))
            ->whereHas('modalidad', fn ($mod) => $mod->where('nombre', $datarow->modalidad))
            ->whereHas('jornada', fn ($jor) => $jor->where('nombre', $jornada))
            ->first();

        if (!$dm) return response('No se encuentra la dependencia correspondiente, favor notificar e intentar más tarde', 500);

        $persona = Persona::where('identificacion', $datarow->numeroDocumento)->first();

        if ($persona) {
            $estudiante = Estudiante::where('idPrograma', $dm->id)->first();
            if ($estudiante) return response('Ya se encuentra registrado en el sistema con este programa', 401);
        }

        $solicitud = SolicitudGrado::where('identificacion_estudiante', $datarow->numeroDocumento)
            ->where('programa_id', $dm->id)
            ->first();
        if ($solicitud) return response('Ya hay una solicitud para este programa', 401);

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

    public function pendientes()
    {
        $estados = Variables::estados();
        $programaIds = session('ur')->usuario->dependenciasModalidades->pluck('id');
        $solicitudes = SolicitudGrado::with('fechaGrado')
            ->whereIn('programa_id', $programaIds)
            ->where('estado_id', $estados['pendiente']->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return $solicitudes;
    }
}
