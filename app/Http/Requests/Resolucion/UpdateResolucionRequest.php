<?php

namespace App\Http\Requests\Resolucion;

use App\Models\Setting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResolucionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('resolucion'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resolucion_type_id' => 'nullable|integer',
            'asunto_type_id' => 'nullable|integer',
            'rd' => 'nullable|string',
            'fecha' => 'nullable|date',
            'asunto' => 'nullable|string',
            'procedencia' => 'nullable|string|max:255',
            'level_modality_id' => 'nullable|integer',

            // Múltiples Interesados
            'interesados' => 'nullable|array',
            'interesados.*.id' => 'nullable|integer',
            'interesados.*.type' => 'nullable|string|in:NaturalPerson,LegalEntity,User,Persona Natural,Persona Juridica,Trabajador UGEL',
            'interesados.*.dni' => 'nullable|string|max:10',
            'interesados.*.cedula' => 'nullable|string|max:20',
            'interesados.*.nombres' => 'nullable|string|max:255',
            'interesados.*.apellido_paterno' => 'nullable|string|max:255',
            'interesados.*.apellido_materno' => 'nullable|string|max:255',
            'interesados.*.ruc' => 'nullable|string|max:11',
            'interesados.*.razon_social' => 'nullable|string|max:255',
            'interesados.*.district' => 'nullable|string|max:255',
            'document_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:'.((int) Setting::getValue('charges_max_file_size', '5') * 1024),
            ],
            'delete_document' => 'nullable|boolean',
        ];
    }
}
