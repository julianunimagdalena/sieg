<?php

namespace App\Tools;

use App\Models\Documento;
use App\Models\Estado;
use App\Models\PazSalvo;
use App\Models\Rol;
use App\Models\TipoEstudiante;

class Variables
{
    static $carpetaDocumentosEstudiantes = 'documentosEstudiantes/';
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
        ];
    }

    static public function documentos()
    {
        return [
            'ecaes' => Documento::where('abrv', 'ECAES')->first()
        ];
    }
}
