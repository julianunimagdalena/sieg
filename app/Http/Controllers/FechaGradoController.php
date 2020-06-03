<?php

namespace App\Http\Controllers;

use App\Models\FechaGrado;
use Illuminate\Http\Request;

class FechaGradoController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['except' => [
            'getFechasActivas'
        ]]);
    }

    public function getFechasActivas()
    {
        $res = [];
        $fechas = FechaGrado::where('estado', 1)->orderBy('fecha_grado')->get();

        foreach ($fechas as $fec) {
            array_push($res, [
                'id' => $fec->id,
                'nombre' => $fec->descripcion
            ]);
        }

        return $res;
    }
}
