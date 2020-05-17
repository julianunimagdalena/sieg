<?php

namespace App\Http\Controllers;

use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();
        // josemartinezar estudiante
        // session(['ur' => UsuarioRol::find(10026)]);
        $this->middleware('auth');
        $this->middleware('rol:' . $roles['estudiante']->nombre);
    }

    public function datos(Request $req)
    {
        $persona = session('ur')->usuario->persona;

        return [
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'genero_id' => $persona->idGenero,
            'etnia' => $persona->etnia,
            'tipo_documento_id' => $persona->tipodoc,
            'identificacion' => $persona->identificacion,
            'lugar_expedicion_documento' => $persona->ciudadExpedicion,
            'fecha_expedicion_documento' => $persona->fecha_expedicion,
            'pais_nacimiento_id' => $persona->municipioNacimiento->departamento->idPais,
            'departamento_nacimiento_id' => $persona->municipioNacimiento->idDepartamento,
            'municipio_nacimiento_id' => $persona->ciudadOrigen,
            'fecha_nacimiento' => $persona->fechaNacimiento,
            'estado_civil_id' => $persona->idEstadoCivil,
            'estrato' => $persona->estrato,
            'pais_residencia_id' => $persona->municipioResidencia->departamento->idPais,
            'departamento_residencia_id' => $persona->municipioResidencia->idDepartamento,
            'municipio_residencia_id' => $persona->ciudadResidencia,
            'direccion' => $persona->direccion,
            'barrio' => $persona->sector,
            'telefono_fijo' => $persona->telefono_fijo,
            'celular' => $persona->celular,
            'celular2' => $persona->celular2,
            'correo' => $persona->correo,
            'correo2' => $persona->correo2,
            'correo_institucional' => $persona->correo_institucional
        ];
    }

    public function datosAcademicos()
    {
        $tipos = Variables::tiposEstudiante();
        $persona = session('ur')->usuario->persona;

        $info_grado = [];
        $estudiantesEgresados = $persona->estudiantes()
            ->where('idTipo', $tipos['egresado']->id)
            ->get();

        foreach ($estudiantesEgresados as $est) {
            $estudio = $est->estudio;

            array_push($info_grado, [
                'codigo' => $est->codigo,
                'programa' => $estudio->programa->nombre,
                'facultad' => $estudio->facultad->nombre,
                'modalidad' => $estudio->modalidad->nombre
            ]);
        }

        $programas = [];
        $estudiantesGraduados = $persona->estudiantes()
            ->where('idTipo', $tipos['graduado']->id)
            ->get();

        foreach ($estudiantesGraduados as $est) {
            $estudio = $est->estudio;

            array_push($programas, [
                'codigo' => $est->codigo,
                'programa' => $estudio->programa->nombre,
                'facultad' => $estudio->facultad->nombre,
                'modalidad' => $estudio->modalidad->nombre
            ]);
        }

        $info_academica = [];
        $hv = $persona->hojaVida;

        if ($hv) {
            foreach ($hv->estudios as $est) {
                array_push($info_academica, [
                    'id' => $est->id,
                    'nombre' => $est->titulo,
                    'institucion' => $est->institucion,
                    'semestres' => $est->duracion,
                    'graduado' => $est->graduado,
                    'anio_culminacion' => $est->anioGrado,
                    'mes_culminacion' => $est->mesGrado
                ]);
            }
        }

        return compact('info_grado', 'programas', 'info_academica');
    }

    public function datosHoja()
    {
        $perfil = null;
        $distinciones = [];
        $asociaciones = [];
        $concejos = [];
        $discapacidades = [];
        $idiomas = [];
        $hoja = session('ur')->usuario->persona->hojaVida;

        if ($hoja) {
            $perfil = $hoja->perfil;

            foreach ($hoja->distinciones as $dis) array_push($distinciones, $dis);
            foreach ($hoja->asociaciones as $asc) array_push($asociaciones, $asc);
            foreach ($hoja->concejos as $con) array_push($concejos, $con);
            foreach ($hoja->discapacidades as $dis) array_push($discapacidades, $dis);

            foreach ($hoja->idiomas as $idm) {
                array_push($idiomas, [
                    'id' => $idm->id,
                    'idioma_id' => $idm->idioma->id,
                    'nivel_habla_id' => $idm->nivelHabla->id,
                    'nivel_escritura_id' => $idm->nivelEscritura->id,
                    'nivel_lectura_id' => $idm->nivelLectura->id
                ]);
            }
        }

        return compact('perfil', 'distinciones', 'asociaciones', 'concejos', 'discapacidades', 'idiomas');
    }

    public function datosLaborales()
    {
        $actualidad_laboral = null;
        $experiencias = [];
        $hoja = session('ur')->usuario->persona->hojaVida;

        if ($hoja) {
            $actualidad_laboral = $hoja->laborando;

            foreach ($hoja->experiencias as $exp) {
                array_push($experiencias, [
                    'id' => $exp->id,
                    'empresa' => $exp->empresa,
                    'cargo' => $exp->cargo,
                    'nivel_cargo_id' => $exp->nivel_cargo_id,
                    'municipio_id' => $exp->municipio_id,
                    'departamento_id' => $exp->municipio->idDepartamento,
                    'pais_id' => $exp->municipio->departamento->idPais,
                    'duracion_id' => $exp->duracion,
                    'tipo_vinculacion_id' => $exp->tipo_vinculacion_id,
                    'salario_id' => $exp->salario_id,
                    'correo' => $exp->email,
                    'telefono' => $exp->telefono,
                    'funciones' => $exp->funcioneslogros
                ]);
            }
        }

        return compact('actualidad_laboral', 'experiencias');
    }
}
