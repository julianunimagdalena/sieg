<?php

namespace App\Tools;

use Carbon\Carbon;
use App\Models\TipoGrado;

class FechaHelper
{
    static public function nombreFecha($fecha)
    {
        $nombre = '';
        $fechaObj = Carbon::parse($fecha->fecha_grado);
        $tipos = Variables::tiposGrado();

        setlocale(LC_TIME, 'Spanish');
        Carbon::setUtf8(true);

        if ($fecha->tipo_grado !== $tipos['no_reporta']->id) {
            $nombre .= ucfirst(strtolower(TipoGrado::find($fecha->tipo_grado)->nombre));
        }

        $nombre .= 
            ' ' . $fechaObj->localeDayOfWeek
            . ' ' . $fechaObj->day
            . ' de ' . $fechaObj->localeMonth
            . ' de ' . $fechaObj->year . '.';

        return $nombre;
    }
}
