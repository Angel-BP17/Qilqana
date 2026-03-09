<?php

namespace App\Http\Requests\Charge;

use App\Models\Charge;
use Illuminate\Foundation\Http\FormRequest;

class DeleteChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('modulo cargos')
            && $this->route('charge')->user_id === $this->user()?->id
            && $this->user()?->hasRole('ADMINISTRADOR');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
