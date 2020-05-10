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
        $fechas = FechaGrado::where('estado', 1)->get();
        return $fechas;
    }
}
