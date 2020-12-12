<?php

namespace App\Http\Requests;

use App\Tools\Variables;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
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
        $roles = Variables::roles();

        return [
            'id' => 'integer',
            'nombres' => 'required',
            'apellidos' => 'required',
            'identificacion' => 'required',
            'username' => 'required',
            'activo' => 'required|boolean',
            'rol_id' => 'required|integer|exists:roles,id',
            'programa_ids' => 'required_if:rol_id,' . $roles['coordinador']->id . '|array',
            'dependencia_ids' => 'required_if:rol_id,' . $roles['dependencia']->id . '|array'
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Obligatorio',
            '*.required_if' => 'Obligatorio'
        ];
    }
}
