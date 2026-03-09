<?php

namespace App\Http\Requests\Resolucion;

use Illuminate\Foundation\Http\FormRequest;

class DeleteResolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('ADMINISTRADOR') ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
