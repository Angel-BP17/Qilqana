<?php

namespace App\Http\Requests\Resolucion;

use App\Models\Resolucion;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateResolucionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Resolucion::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resolucion_type_id' => 'required|exists:resolucion_types,id',
            'asunto_type_id' => [
                'nullable',
                Rule::exists('asunto_type_resolucion_type', 'asunto_type_id')
                    ->where('resolucion_type_id', $this->resolucion_type_id),
            ],
            'rd' => [
                'required',
                'string',
                Rule::unique('resolucions')->where(function ($query) {
                    return $query->where('periodo', Carbon::parse($this->fecha)->year)
                        ->where('resolucion_type_id', $this->resolucion_type_id);
                }),
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string',
            'procedencia' => 'nullable|string|max:255',
            'level_modality_id' => 'nullable|exists:level_modalities,id',

            // Múltiples Interesados
            'interesados' => 'required|array|min:1',
            'interesados.*.id' => 'nullable|integer',
            'interesados.*.type' => 'required|string|in:NaturalPerson,LegalEntity,User,Persona Natural,Persona Juridica,Trabajador UGEL',
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
                'max:' . ((int) \App\Models\Setting::getValue('charges_max_file_size', '5') * 1024),
            ],
        ];
    }
}
