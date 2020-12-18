<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FechaGradoRequest extends FormRequest
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
            'id' => 'integer|exists:fechas_de_grado,id',
            'fecha' => 'required|date',
            'inscripcion_fecha_inicio' => 'required|date|before_or_equal:fecha',
            'inscripcion_fecha_fin' => 'required|date|after_or_equal:inscripcion_fecha_inicio',
            'doc_est_fecha_fin' => 'required|date|after_or_equal:inscripcion_fecha_inicio',
            'paz_salvo_fecha_fin' => 'required|date|after_or_equal:inscripcion_fecha_inicio',
            'direccion_prog_fecha_fin' => 'required|date|after_or_equal:inscripcion_fecha_inicio',
            'secretaria_gen_fecha_fin' => 'required|date|after_or_equal:direccion_prog_fecha_fin',
            'tipo_grado_id' => 'required|integer|exists:tipos_de_grados,id',
            'estado' => 'required|boolean',
            'nombre' => 'required',
            'observacion' => ''
        ];
    }
}
