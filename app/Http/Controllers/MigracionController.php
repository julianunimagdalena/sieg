<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\DependenciaModalidad;
use App\Models\DistincionEstudiante;
use App\Models\EstadoCivil;
use App\Models\Estudiante;
use App\Models\Estudio;
use App\Models\FechaGrado;
use App\Models\Genero;
use App\Models\HojaVida;
use App\Models\Jornada;
use App\Models\ModalidadEstudio;
use App\Models\Municipio;
use App\Models\NivelEstudio;
use App\Models\Persona;
use App\Models\ProcesoGrado;
use App\Models\TipoDocumento;
use App\Models\TipoGrado;
use App\Tools\Variables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigracionController extends Controller
{
    private function hallarDM($data)
    {
        $res = (object) ['dm' => null, 'message' => ''];
        $programa = Dependencia::where('nombre', $data->programa)->first();
        $facultad = null;

        if (!$programa) {
            $res->message = 'No se encuentra el programa con nombre ' . $data->programa;
            return $res;
        }



        $modalidad = ModalidadEstudio::where('abrv', $data->abrv_modalidad_estudio)->first();
        $jornada = Jornada::where('nombre', $data->jornada)->first();

        // $res->dm = DependenciaModalidad::where('idPrograma', $programa->id)
        //     ->where('idFacultad', $facultad->id)
        //     ->where('idModalidad', $modalidad->id)
        //     ->where('idJornada', $jornada->id)
        //     ->first();
        $dm = DependenciaModalidad::where('idPrograma', $programa->id);

        if ($dm->count() > 1) {
            $dm = $dm->where('idJornada', $jornada->id);

            if ($dm->count() > 1) {
                $dm = $dm->where('idModalidad', $modalidad->id);

                if ($dm->count() > 1) {
                    $facultad = Dependencia::where('nombre', $data->facultad)->first();

                    if (!$facultad) {
                        $res->message = 'No se encuentra la facultad con nombre ' . $data->facultad;
                        return $res;
                    }

                    $dm = $dm->where('idFacultad', $facultad->id);
                }
            }
        }

        $res->dm = $dm->first();

        if (!$res->dm) {
            $res->message = 'No se encuentra dependencia_modalidad para:'
                . ' PROGRAMA: ' . $programa->nombre
                . ' FACULTAD: ' . $facultad->nombre
                . ' JORNADA: ' . $jornada->nombre
                . ' MODALIDAD: ' . $modalidad->nombre;
        }

        return $res;
    }

    private function registrarHojaVida($persona, $data)
    {
        $hoja = $persona->hojaVida ?? new HojaVida();
        $hoja->activa = 0;
        $hoja->perfil = $data->perfil;
        $hoja->laborando = $data->labora;
        $hoja->idPersona = $persona->id;
        $hoja->save();

        $old_data = [];
        $old_data['estudios'] = DB::connection('old')->table('Hvestudios')
            ->join('Tnivelestudios as ne', 'ne.IdNivelEstudio', '=', 'Hvestudios.IdTNivelEstudio')
            ->where('IdPersona', $data->bd_id)
            ->select(
                'Hvestudios.NombredelEstudio as nombre',
                'Hvestudios.Institucion as institucion',
                'Hvestudios.SemestresCursados as duracion',
                'Hvestudios.Graduado as graduado',
                'Hvestudios.AnioFinCurso as anio',
                'Hvestudios.MesFinCurso as mes',
                'ne.Nombre as nivel_estudio',
            )
            ->get();

        foreach ($old_data['estudios'] as $est) {
            $mes = ((int) $est->mes) - 1;
            $mes = ($mes > 0 && $mes < 11) ? $mes : null;
            $graduado = $est->graduado === 'SI';
            $ne = NivelEstudio::where('nombre', $est->nivel_estudio)->first();

            $estudio = $hoja->estudios()->where('titulo', $est->nombre)->first() ?? new Estudio();
            $estudio->titulo = $est->nombre;
            $estudio->institucion = $est->institucion;
            $estudio->duracion = $est->duracion;
            $estudio->anioGrado = $est->anio;
            $estudio->mesGrado = $mes;
            $estudio->graduado = $graduado;
            $estudio->nivel_estudio_id = $ne->id;
            $estudio->idHoja = $hoja->id;
            $estudio->save();
        }
    }

    private function registrarGraduado($data, $registrar_hv = false)
    {
        $errorMessage = '';
        $res = (object) ['status' => null];
        $resDM = $this->hallarDM($data);
        $dm = $resDM->dm;

        if ($dm) {
            try {
                $updated = true;
                $genero = Genero::where('nombre', $data->genero)->first();
                $ec = EstadoCivil::where('nombre', $data->estado_civil)->first();
                $td = TipoDocumento::where('nombre', $data->tipo_documento)->first();
                $municipio_nacimiento = Municipio::where('nombre', $data->municipio_nacimiento)
                    ->whereHas('departamento', fn ($dep) => $dep->where('nombre', $data->departamento_nacimiento))
                    ->first();
                $municipio_residencia = Municipio::where('nombre', $data->municipio_residencia)
                    ->whereHas('departamento', fn ($dep) => $dep->where('nombre', $data->departamento_residencia))
                    ->first();
                $distincion = DistincionEstudiante::where('nombre', $data->distincion)->first();
                $tipo_grado = TipoGrado::where('nombre', $data->tipo_grado)->first();

                $persona = Persona::where('identificacion', $data->identificacion)->first() ?? new Persona();
                $persona->nombres = $data->nombres;
                $persona->apellidos = $data->apellidos;
                $persona->fechaNacimiento = $data->fecha_nacimiento;
                $persona->telefono_fijo = $data->telefono;
                $persona->correo = $data->correo;
                $persona->correo2 = $data->correo2;
                $persona->celular = $data->celular;
                $persona->celular2 = $data->celular2;
                $persona->direccion = $data->direccion;
                $persona->sector = $data->sector;
                $persona->estrato = $data->estrato;
                $persona->estadovida = $data->estado_vida;
                $persona->identificacion = $data->identificacion;
                $persona->fecha_expedicion = $data->fecha_expedicion_documento;
                $persona->ciudadExpedicion = $data->ciudad_expedicion_documento;
                $persona->etnia = $data->etnia;
                $persona->idGenero = $genero->id;
                $persona->idEstadoCivil = $ec->id;
                $persona->tipodoc = $td->id;
                $persona->ciudadOrigen = $municipio_nacimiento ? $municipio_nacimiento->id : null;
                $persona->ciudadResidencia = $municipio_residencia ? $municipio_residencia->id : null;
                $persona->save();

                if ($registrar_hv) $this->registrarHojaVida($persona, $data);

                $tiposEstudiante = Variables::tiposEstudiante();
                $estudiante = Estudiante::where('codigo', $data->codigo)
                    ->where('idPrograma', $dm->id)
                    ->first();

                if (!$estudiante) {
                    $updated = false;
                    $estudiante = new Estudiante();
                }

                $estudiante->codigo = $data->codigo;
                $estudiante->folio = $data->folio;
                $estudiante->acta = $data->acta;
                $estudiante->libro = $data->libro;
                $estudiante->idTipo = $tiposEstudiante['graduado']->id;
                $estudiante->distincion = $distincion->id;
                $estudiante->idPersona = $persona->id;
                $estudiante->idPrograma = $dm->id;
                $estudiante->save();

                $fecha = FechaGrado::where('fecha_grado', $data->fecha_grado)
                    ->where('tipo_grado', $tipo_grado->id)
                    ->first();

                if (!$fecha) {
                    $fecObj = Carbon::parse($data->fecha_grado)->locale('es_ES');
                    $fecha = new FechaGrado();
                    $fecha->nombre = $fecObj->isoFormat('dddd DD MMMM YYYY');
                    $fecha->anio = $fecObj->year;
                    $fecha->tipo_grado = $tipo_grado->id;
                    $fecha->estado = 0;
                    $fecha->fecha_grado = $data->fecha_grado;
                    $fecha->save();
                }

                $estados = Variables::estados();
                $proceso = $estudiante->procesoGrado ?? new ProcesoGrado();
                $proceso->estado_ficha = true;
                $proceso->estado_encuesta = true;
                $proceso->estado_secretaria_id = $estados['aprobado']->id;
                $proceso->estado_programa_id = $estados['aprobado']->id;
                $proceso->idEstudiante = $estudiante->id;
                $proceso->idFecha = $fecha->id;
                $proceso->save();

                $res->status = $updated ? 'updated' : 'registered';
            } catch (\Throwable $th) {
                $errorMessage = $th->getMessage();
            }
        } else {
            $errorMessage = $resDM->message;
        }

        if ($errorMessage) {
            Storage::append('logs/registro_graduados.log', "$data->nombres $data->apellidos - $data->programa: $errorMessage");
            $res->status = 'error';
        }

        return $res;
    }

    public function migrarEstudiantes(Request $request)
    {
        $limit = 10;
        $actualizados = 0;
        $registrados = 0;
        $errores = 0;
        $graduados = DB::connection('old')->table('Graduados')
            ->join('Estudiantesxprogramas as ep', 'ep.CodigoEstudiante', '=', 'Graduados.CodigoEstudiante')
            ->join('Personas as p', 'p.IdPersona', '=', 'ep.IdPersona')
            ->join('Tgeneros as g', 'g.IdGenero', '=', 'p.IdGenero')
            ->join('Testadociviles as ec', 'ec.IdEstadoCivil', '=', 'p.IdTEstadoCivil')
            ->leftJoin('Municipios as mnacimiento', function ($join) {
                $join->on('mnacimiento.IdMunicipio', '=', 'p.IdMunicipioNacimiento')
                    ->on('mnacimiento.IdDepartamento', '=', 'p.IdDepartamentoNacimiento');
            })
            ->leftJoin('Departamentos as dnacimiento', 'dnacimiento.IdDepartamento', '=', 'p.IdDepartamentoNacimiento')
            ->leftJoin('Municipios as mresidencia', function ($join) {
                $join->on('mresidencia.IdMunicipio', '=', 'p.IdMunicipioDireccion')
                    ->on('mresidencia.IdDepartamento', '=', 'p.IdDepartamentoDireccion');
            })
            ->leftJoin('Departamentos as dresidencia', 'dresidencia.IdDepartamento', '=', 'p.IdDepartamentoDireccion')
            ->join('Identificacionxpersonas as ixp', 'ixp.IdPersona', '=', 'p.IdPersona')
            ->join('Tidentificaciones as td', 'td.IdIdentificacion', '=', 'ixp.IdTIdentificacion')
            ->leftJoin('Municipios as mdocumento', function ($join) {
                $join->on('mdocumento.IdMunicipio', '=', 'ixp.IdMunicipio')
                    ->on('mdocumento.IdDepartamento', '=', 'ixp.IdDepartamento');
            })
            ->join('Programas as prg', function ($join) {
                $join->on('prg.IdPrograma', '=', 'ep.IdPrograma')
                    ->on('prg.IdFacultad', '=', 'ep.IdFacultad')
                    ->on('prg.Jornada', '=', 'ep.Jornada');
            })
            ->join('Facultades as fac', 'fac.IdFacultad', '=', 'ep.IdFacultad')
            ->leftJoin('Tdistinciones as dis', 'dis.IdDistincion', '=', 'Graduados.IdTDistincion')
            ->leftJoin('TipodeGraduacion as tg', 'tg.IdTipodeGraduacion', '=', 'Graduados.IdTipodeGraduacion')
            ->leftJoin('Hvdatosbasicos as hv', 'hv.IdPersona', '=', 'p.IdPersona')
            ->leftJoin('Tetnias as etnia', 'etnia.IdEtnia', '=', 'hv.IdEtnia')
            ->select(
                'p.IdPersona as bd_id',
                'p.Nombres as nombres',
                'p.Apellidos as apellidos',
                'p.FechaNacimiento as fecha_nacimiento',
                'p.Telefono as telefono',
                'p.Email as correo',
                'p.Email2 as correo2',
                'p.Celular as celular',
                'p.Celular2 as celular2',
                'p.Direccion as direccion',
                'p.Barrio as sector',
                'p.Estrato as estrato',
                'p.EstadodeVida as estado_vida',
                'ixp.NumeroDocumento as identificacion',
                'ixp.FechaExpedicion as fecha_expedicion_documento',
                'mdocumento.Nombre as ciudad_expedicion_documento',
                'g.Nombre as genero',
                'ec.Nombre as estado_civil',
                'td.Nombre as tipo_documento',
                'mnacimiento.Nombre as municipio_nacimiento',
                'dnacimiento.Nombre as departamento_nacimiento',
                'mresidencia.Nombre as municipio_residencia',
                'dresidencia.Nombre as departamento_residencia',
                'ep.CodigoEstudiante as codigo',
                'Graduados.Folio as folio',
                'Graduados.Acta as acta',
                'Graduados.Libro as libro',
                'Graduados.FechaGrado as fecha_grado',
                'prg.Nombre as programa',
                'fac.Nombre as facultad',
                'prg.IdModalidadEstudio as abrv_modalidad_estudio',
                'prg.Jornada as jornada',
                'dis.Nombre as distincion',
                'tg.Nombre as tipo_grado',
                'hv.Perfil as perfil',
                'hv.Labora as labora',
                'etnia.Nombre as etnia',
            )
            ->take($limit)
            ->get();
        // ->count();
        // return $graduados;

        foreach ($graduados as $graduado) {
            if ($graduado->genero === 'POR DEFINIR') $graduado->genero = 'OTRO';
            if ($graduado->facultad === 'FACULTAD DE INGENIERÍA') $graduado->facultad = 'FACULTAD DE INGENIERIA';

            $res = $this->registrarGraduado($graduado, $request->registrar_hv);

            switch ($res->status) {
                case 'updated':
                    $actualizados++;
                    break;

                case 'registered':
                    $registrados++;
                    break;

                case 'error':
                    $errores++;
                    break;
            }
        }

        return compact('actualizados', 'registrados', 'errores');
    }
}
