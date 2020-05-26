<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsistenciaCeremoniaRequest extends FormRequest
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
            'pg_id' => 'required|integer|exists:proceso_grado,id',
            'confirmacion_asistencia' => 'required|boolean',
            'talla_camisa' => 'required_if:confirmacion_asistencia,true',
            'estatura' => 'required_if:confirmacion_asistencia,true|numeric',
            'tamano_birrete' => 'required_if:confirmacion_asistencia,true',
            'num_acompanantes' => 'required_if:confirmacion_asistencia,true'
        ];
    }

    public function messages()
    {
        return [
            '*.required_if' => 'Obligatorio',
            '*.required' => 'Obligatorio'
        ];
    }
}
