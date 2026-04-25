<?php

namespace App\Http\Requests\NaturalPerson;

use Illuminate\Foundation\Http\FormRequest;

class ImportNaturalPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->hasRole('ADMINISTRADOR') ||
            $user?->can('natural-people.create');
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls'],
            'update_existing' => ['nullable', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo_excel.required' => 'Debe seleccionar un archivo para importar.',
            'archivo_excel.mimes' => 'El archivo debe ser .xlsx o .xls.',
        ];
    }
}
