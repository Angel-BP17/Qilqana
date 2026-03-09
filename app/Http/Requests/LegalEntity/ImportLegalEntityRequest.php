<?php

namespace App\Http\Requests\LegalEntity;

use Illuminate\Foundation\Http\FormRequest;

class ImportLegalEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user?->hasRole('ADMINISTRADOR') ||
            $user?->can('legal-entities.create');
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
