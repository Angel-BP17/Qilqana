<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResolucionTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('resolucion_type')->id;

        return [
            'name' => 'required|string|max:255|unique:resolucion_types,name,'.$id,
            'abreviacion' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo de resolución con este nombre.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
        ];
    }
}
