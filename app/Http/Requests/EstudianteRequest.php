<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudianteRequest extends FormRequest
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
            'id' => 'exists:estudiantes,id',
            'persona_id' => 'required|exists:personas,id',
            'facultad_id' => 'required|exists:dependencias,id',
            'programa_id' => 'required|exists:dependencias,id',
            'jornada_id' => 'required|exists:jornadas,id',
            'modalidad_id' => 'required|exists:modalidades_estudio,id',
            'codigo' => 'required',
            'folio' => '',
            'acta' => '',
            'libro' => '',
            'distincion_id' => 'required|exists:distinciones_estudiantes,id',
            'fecha_grado' => 'required'
        ];
    }
}
