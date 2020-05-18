<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonaRequest extends FormRequest
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
            'fecha_expedicion_documento' => 'required|date',
            'estado_civil_id' => 'required|exists:estadocivil,id',
            'genero_id' => 'required|exists:genero,id',
            'direccion' => 'required',
            'barrio' => 'required',
            'municipio_residencia_id' => 'required|exists:municipios,id',
            'estrato' => 'required',
            'telefono_fijo' => '',
            'celular' => 'required',
            'celular2' => '',
            'correo' => 'required',
            'correo2' => ''
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Obligatorio',
            '*.exists' => 'No valido'
        ];
    }
}
