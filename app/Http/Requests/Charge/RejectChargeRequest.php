<?php

namespace App\Http\Requests\Charge;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RejectChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $charge = $this->route('charge');

        return $charge
            && $this->user()?->can('modulo cargos')
            && $charge->signature
            && $charge->signature->assigned_to === $this->user()?->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'signature_comment' => 'required|string|max:1000',
        ];
    }
}
