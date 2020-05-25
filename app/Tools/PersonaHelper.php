<?php

namespace App\Tools;

use Carbon\Carbon;

class PersonaHelper
{
    static public function actualizarProgresoFicha($persona)
    {
        $progreso = $persona->progreso_ficha;

        if ($progreso === 100) {
            $tipos = Variables::tiposEstudiante();
            $estudiantes = $persona->estudiantes()->where('idTipo', $tipos['egresado']->id)->get();

            foreach ($estudiantes as $est) {
                $pg = $est->procesoGrado;

                if (!$pg->estado_ficha) {
                    $pg->estado_ficha = 1;
                    $pg->fecha_ficha = Carbon::now();
                    $pg->save();
                }
            }
        }
    }
}
