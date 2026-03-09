<?php

namespace App\Http\Requests\Resolucion;

use Illuminate\Foundation\Http\FormRequest;

class CreateResolucionChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('ADMINISTRADOR')
            || $this->user()?->can('modulo resoluciones');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // No specific validation rules needed for this request
        ];
    }
}
