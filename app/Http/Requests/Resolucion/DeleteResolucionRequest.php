<?php

namespace App\Http\Requests\Resolucion;

use Illuminate\Foundation\Http\FormRequest;

class DeleteResolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('resolucion'));
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
