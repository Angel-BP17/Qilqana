<?php

namespace App\Http\Requests\Resolucion;

use Illuminate\Foundation\Http\FormRequest;

class ImportResolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('ADMINISTRADOR') || $this->user()?->can('resolucion importar excel');
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls'],
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
