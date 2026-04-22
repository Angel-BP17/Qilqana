<?php

namespace App\Http\Requests\Resolucion;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rd' => [
                'required',
                'string',
                Rule::unique('resolucions')->where(function ($query) {
                    return $query->where('periodo', Carbon::parse($this->fecha)->year);
                })->ignore($this->resolucion)
            ],
            'fecha' => 'required|date',
            'asunto' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'dni' => 'required|string|min:8|max:10|regex:/^\d{8,10}$/',
            'procedencia' => 'nullable|string|max:255',
        ];
    }
}
