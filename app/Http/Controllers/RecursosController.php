<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

use App\Models\Duracion;
use App\Models\EstadoCivil;
use App\Models\Genero;
use App\Models\Idioma;
use App\Models\Municipio;
use App\Models\NivelCargo;
use App\Models\NivelIdioma;
use App\Models\Pais;
use App\Models\Salario;
use App\Models\TipoDocumento;
use App\Models\TipoVinculacion;

class RecursosController extends Controller
{
    public function __construct()
    {
    }

    public function duracionesLaborales()
    {
        return Duracion::all();
    }

    public function idiomas()
    {
        return Idioma::all();
    }

    public function nivelesCargo()
    {
        return NivelCargo::all();
    }

    public function nivelesIdioma()
    {
        return NivelIdioma::all();
    }

    public function paises()
    {
        return Pais::orderBy('nombre')->get();
    }

    public function departamentos(Request $request)
    {
        $this->validate($request, [
            'pais' => 'exists:paises,id'
        ]);

        $departamentos = new Departamento();
        if ($request->pais) $departamentos = $departamentos->where('idPais', $request->pais);

        return $departamentos->orderBy('nombre')->get();
    }

    public function municipios(Request $request)
    {
        $this->validate($request, [
            'departamento' => 'exists:departamentos,id'
        ]);

        $municipios = new Municipio();
        if ($request->departamento) $municipios = $municipios->where('idDepartamento', $request->departamento);

        return $municipios->orderBy('nombre')->get();
    }

    public function salarios()
    {
        return Salario::all();
    }

    public function tiposVinculacion()
    {
        return TipoVinculacion::all();
    }

    public function generos()
    {
        return Genero::all();
    }

    public function tiposDocumento()
    {
        return TipoDocumento::all();
    }

    public function estadosCiviles()
    {
        return EstadoCivil::all();
    }
}