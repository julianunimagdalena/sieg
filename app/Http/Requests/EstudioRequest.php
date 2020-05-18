<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudioRequest extends FormRequest
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
            'id' => 'exists:estudiosrealizados,id',
            'nombre' => 'required',
            'institucion' => 'required',
            'meses' => 'required',
            'graduado' => 'required|boolean',
            'anio_culminacion' => 'required_if:graduado,true|integer',
            'mes_culminacion' => 'required_if:graduado,true'
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Obligatorio'
        ];
    }
}
