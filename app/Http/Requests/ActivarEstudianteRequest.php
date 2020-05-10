<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivarEstudianteRequest extends FormRequest
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
            'solicitud_id' => 'required_without_all:codigo,fecha_grado_id|exists:solicitud_grado,id',
            'codigo' => 'required_without:solicitud_id|numeric',
            'fecha_grado_id' => 'required_without:solicitud_id|exists:fechas_de_grado,id'
        ];
    }
}
