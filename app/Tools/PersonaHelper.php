<?php

namespace App\Tools;

use Carbon\Carbon;

class PersonaHelper
{
    static public function actualizarProgresoFicha($persona)
    {
        $tipos = Variables::tiposEstudiante();
        $progreso = $persona->progreso_ficha;
        $estudiantes = $persona->estudiantes()->where('idTipo', $tipos['egresado']->id)->get();

        foreach ($estudiantes as $est) {
            $pg = $est->procesoGrado;

            if ($progreso === 100) {
                if (!$pg->estado_ficha) {
                    $pg->estado_ficha = true;
                    $pg->fecha_ficha = Carbon::now();
                }
            } else {
                $pg->estado_ficha = false;
                $pg->fecha_ficha = null;
            }

            $pg->save();
        }
    }
}
