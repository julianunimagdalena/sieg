<?php

namespace App\Tools;

use App\Models\Documento;
use App\Models\Estado;
use App\Models\PazSalvo;
use App\Models\Rol;
use App\Models\TipoDependencia;
use App\Models\TipoEstudiante;
use App\Models\TipoGrado;

class Variables
{
    static $carpetaDocumentosEstudiantes = 'documentosEstudiantes/';
    static $estadoNoAprobado = 'NO APROBADO';

    static public function roles()
    {
        return [
            'coordinador' => Rol::where('nombre', 'Coordinador de programa')->first(),
            'estudiante' => Rol::where('nombre', 'Estudiante')->first(),
            'administrador' => Rol::where('nombre', 'Administrador Egresados')->first(),
            'secretariaGeneral' => Rol::where('nombre', 'Secretaría General')->first(),
            'dependencia' => Rol::where('nombre', 'Dependencia')->first()
        ];
    }

    static public function estados()
    {
        return [
            'pendiente' => Estado::where('nombre', 'PENDIENTE')->first(),
            'aprobado' => Estado::where('nombre', 'APROBADO')->first(),
            'rechazado' => Estado::where('nombre', 'RECHAZADO')->first(),
            'sin_cargar' => Estado::where('nombre', 'SIN CARGAR')->first()
        ];
    }

    /**
     * @return array [
     *      egresado,
     *      graduado
     * ]
     */
    static public function tiposEstudiante()
    {
        return [
            'egresado' => TipoEstudiante::where('nombre', 'Egresado')->first(),
            'graduado' => TipoEstudiante::where('nombre', 'Graduado')->first(),
        ];
    }

    static public function defaultPazSalvos()
    {
        return [
            'biblioteca' => PazSalvo::where('nombre', 'BIBLIOTECA')->first(),
            'recursosEducativos' => PazSalvo::where('nombre', 'GRUPO DE RECURSOS EDUCATIVOS')->first(),
            'bienestar' => PazSalvo::where('nombre', 'BIENESTAR UNIVERSITARIO')->first(),
            'pago' => PazSalvo::where('nombre', 'PAGO DERECHO A GRADO')->first(),
        ];
    }

    static public function documentos()
    {
        return [
            'ecaes' => Documento::where('abrv', 'ECAES')->first(),
            'identificacion' => Documento::where('abrv', 'DOCIDENTIDAD')->first(),
            'paz_salvos' => Documento::where('abrv', 'PAZSALVOS')->first(),
            'ficha' => Documento::where('abrv', 'FICHA')->first(),
            'titulo_grado' => Documento::where('abrv', 'TITULOGRADO')->first(),
            'ayre' => Documento::where('abrv', 'PSAYRE')->first(),
        ];
    }

    static public function documentosCanGenerar()
    {
        $documentos = Variables::documentos();

        return [
            $documentos['paz_salvos']->id,
            $documentos['ficha']->id,
            $documentos['ayre']->id
        ];
    }

    static public function documentosCanCargarDireccion()
    {
        $documentos = Variables::documentos();

        return [
            $documentos['titulo_grado']->id
        ];
    }

    static public function documentosCanCambiarEstado()
    {
        $documentos = Variables::documentos();

        return [
            $documentos['ecaes']->id
        ];
    }

    static public function tiposDependencia()
    {
        return [
            'dir_programa' => TipoDependencia::where('nombre', 'Dirección de programa')->first(),
            'administrativa' => TipoDependencia::where('nombre', 'Administrativa')->first(),
            'facultad' => TipoDependencia::where('nombre', 'Facultad')->first()
        ];
    }

    static public function tiposGrado()
    {
        return [
            'ceremonia' => TipoGrado::where('nombre', 'Ceremonia')->first(),
            'postumo' => TipoGrado::where('nombre', 'Postumo')->first(),
            'privado' => TipoGrado::where('nombre', 'Privado')->first(),
            'ventanilla' => TipoGrado::where('nombre', 'Ventanilla')->first(),
            'extratemporaneo' => TipoGrado::where('nombre', 'Ventanilla extemporáneo')->first(),
            'no_reporta' => TipoGrado::where('nombre', 'NO REPORTA')->first()
        ];
    }
}
