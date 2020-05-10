<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivarEstudianteRequest;
use App\Models\Estudiante;
use App\Models\Genero;
use App\Models\Municipio;
use App\Models\Persona;
use App\Models\ProcesoGrado;
use App\Models\SolicitudGrado;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Tools\Variables;
use App\Tools\WSAdmisiones;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirProgramaController extends Controller
{
    public function __constructor()
    {
        $roles = Variables::roles();

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['coordinador']->nombre);
    }

    public function activarEstudiante(ActivarEstudianteRequest $request)
    {
        $solicitud = null;
        $codigo = '';
        $programa_id = NAN;
        $fecha_grado_id = NAN;

        if ($request->solicitud_id) {
            $solicitud = SolicitudGrado::find($request->solicitud_id);
            $codigo = $solicitud->codigo_estudiante;
            $programa_id = $solicitud->programa_id;
            $fecha_grado_id = $solicitud->fecha_grado_id;
        }

        $estudiante = Estudiante::where('codigo', $codigo)->first();
        if ($estudiante) return response('El estudiante ya se encuentra en el sistema', 400);

        $ws = new WSAdmisiones();
        $estados = Variables::estados();
        $roles = Variables::roles();
        $pazSalvos = Variables::defaultPazSalvos();
        $tiposEstudiante = Variables::tiposEstudiante();
        $dataEstudiante = $ws->getInfoEstudianteByCodigo($codigo)[0];
        $dataGraduado = $ws->getInformacionGraduadoByCodigo($codigo)[0];
        $persona = Persona::where('identificacion', $dataEstudiante->numDocumento)->first();

        if (!$persona) {
            $ciudadResidencia = Municipio::where('nombre', $dataGraduado->ciudad)->first();
            $ciudadOrigen = Municipio::where('nombre', $dataEstudiante->ciudadOrigen)->first();
            $tipoDoc = TipoDocumento::where('abrv', $dataEstudiante->tipoDoc)->first();
            $genero = $dataEstudiante->genero;

            if ($genero === 'M') $genero = 'MASCULINO';
            else if ($genero === 'F') $genero = 'FEMENINO';
            else $genero = 'OTRO';

            $persona = new Persona();
            $persona->nombres = $dataEstudiante->nombres;
            $persona->apellidos = $dataEstudiante->apellidos;
            $persona->correo = $dataEstudiante->email;
            $persona->correo_institucional = $dataEstudiante->email;
            $persona->celular = $dataEstudiante->celular;
            $persona->telefono_fijo = $dataEstudiante->telefono;
            $persona->identificacion = $dataEstudiante->numDocumento;
            $persona->ciudadExpedicion = $dataGraduado->ciudadCedula;
            $persona->ciudadResidencia = $ciudadResidencia ? $ciudadResidencia->id : null;
            $persona->ciudadOrigen = $ciudadOrigen ? $ciudadOrigen->id : null;
            $persona->fechaNacimiento = Carbon::createFromFormat('d/m/Y', $dataEstudiante->fecNacimiento);
            $persona->direccion = $dataEstudiante->direccion;
            $persona->estrato = $dataEstudiante->estrato;
            $persona->idGenero = Genero::where('nombre', $genero)->first()->id;
            $persona->tipodoc = $tipoDoc->id;
            $persona->save();
        }

        if (!$persona->usuario) {
            $user = new User();
            $user->idPersona = $persona->id;
            $user->identificacion = explode('@', $persona->correo_institucional)[0];
            $user->activo = true;
            $user->save();
        }

        $persona = Persona::find($persona->id);
        $rol = $persona->usuario->roles()->find($roles['estudiante']->id);

        if (!$rol) $persona->usuario->roles()->attach($roles['estudiante']->id, ['activo' => true]);

        $zonal = $dataEstudiante->zonal === 'NO DEFINIDO' ? 'SANTA MARTA' : $dataEstudiante->zonal;
        $municipioZonal = Municipio::where('nombre', $zonal)->first();
        $estudiante = new Estudiante();
        $estudiante->codigo = $dataEstudiante->codigo;
        $estudiante->idTipo = $tiposEstudiante['egresado']->id;
        $estudiante->idPrograma = $programa_id;
        $estudiante->idPersona = $persona->id;
        $estudiante->idZonal = $municipioZonal->id;
        $estudiante->save();

        $estudiante->pazSalvos()->attach([
            $pazSalvos['biblioteca']->id,
            $pazSalvos['bienestar']->id,
            $pazSalvos['recursosEducativos']->id
        ]);

        $proceso = new ProcesoGrado();
        $proceso->idEstudiante = $estudiante->id;
        $proceso->idFecha = $fecha_grado_id;
        $proceso->save();

        if ($solicitud) {
            $solicitud->estado_id = $estados['aprobado']->id;
            $solicitud->fecha_realizacion = Carbon::now();
            $solicitud->save();
        }

        return 'ok';
    }
}
