<?php

namespace App\Http\Requests\AsuntoType;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsuntoTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('ADMINISTRADOR');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:asunto_types,name',
            'description' => 'nullable|string',
            'resolucion_type_ids' => 'required|array|min:1',
            'resolucion_type_ids.*' => 'exists:resolucion_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tipo de asunto es obligatorio.',
            'name.unique' => 'Este tipo de asunto ya está registrado.',
            'resolucion_type_ids.required' => 'Debe seleccionar al menos un tipo de resolución.',
        ];
    }
}
