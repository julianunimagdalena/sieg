<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudGradoRequest extends FormRequest
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
            'identificacion' => 'required|numeric',
            'programa' => 'required',
            'fecha_id' => 'required|exists:fechas_de_grado,id',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Campo obligatorio'
        ];
    }
}
