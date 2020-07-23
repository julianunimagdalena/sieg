<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CargaRequest extends FormRequest
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
            'programa_id' => 'integer|required|exists:dependencias,id',
            'value' => 'boolean|required',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Obligatorio'
        ];
    }
}
