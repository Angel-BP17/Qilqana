<?php

namespace App\Http\Requests\Charge;

use App\Models\Charge;
use App\Models\Setting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Charge::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_interesado' => ['required', 'in:Persona Juridica,Persona Natural,Trabajador UGEL'],
            'ruc' => ['required_if:tipo_interesado,Persona Juridica', 'nullable', 'string', 'max:255'],
            'razon_social' => ['required_if:tipo_interesado,Persona Juridica', 'nullable', 'string', 'max:255'],
            'district' => ['required_if:tipo_interesado,Persona Juridica', 'nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'dni' => ['required_if:tipo_interesado,Persona Natural,', 'nullable', 'string', 'min:8', 'max:10', 'regex:/^\d{8,10}$/'],
            'nombres' => ['required_if:tipo_interesado,Persona Natural,', 'nullable', 'string', 'max:255'],
            'apellido_paterno' => ['required_if:tipo_interesado,Persona Natural,', 'nullable', 'string', 'max:255'],
            'apellido_materno' => ['required_if:tipo_interesado,Persona Natural,', 'nullable', 'string', 'max:255'],
            'asunto' => ['required', 'string', 'max:255'],
            'document_date' => ['nullable', 'date'],
            'assigned_to' => ['required_if:tipo_interesado,Trabajador UGEL', 'nullable', Rule::exists('users', 'id')],
            'representative_dni' => ['nullable', 'string', 'max:10'],
            'representative_nombres' => ['nullable', 'string', 'max:255'],
            'representative_apellido_paterno' => ['nullable', 'string', 'max:255'],
            'representative_apellido_materno' => ['nullable', 'string', 'max:255'],
            'representative_cargo' => ['nullable', 'string', 'max:255'],
            'representative_since' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $period = Setting::getValue('charge_period', '');
            if ($period === '') {
                $validator->errors()->add('charge_period', 'Debe configurar el periodo de cargos antes de crear uno.');
            }
        });
    }
}
