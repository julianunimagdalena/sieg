<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExperienciaLaboralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'exists:experiencias_laborales,id',
            'empresa' => 'required',
            'cargo' => 'required',
            'nivel_cargo_id' => 'required|exists:niveles_cargo,id',
            'municipio_id' => 'required|exists:municipios,id',
            'duracion_id' => 'required|exists:duraciones,id',
            'tipo_vinculacion_id' => 'required|exists:tipos_vinculacion,id',
            'salario_id' => 'required|exists:salarios,id',
            'correo' => 'required',
            'telefono' => 'required',
            'funciones' => 'required'
        ];
    }
}
