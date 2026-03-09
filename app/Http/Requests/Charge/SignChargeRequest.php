<?php

namespace App\Http\Requests\Charge;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $charge = $this->route('charge');
        $assignedTo = $charge?->signature?->assigned_to;

        return $user && $charge && (
            $user->hasRole('ADMINISTRADOR') ||
            // Evitar 500 cuando no existe firma asociada.
            ($assignedTo && $user->id === $assignedTo && $user->canAny(['modulo cargos', 'modulo resoluciones']))
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $charge = $this->route('charge');
        $requiresPoder = !$this->boolean('titularidad')
            && in_array($charge?->tipo_interesado, ['Persona Juridica', 'Persona Natural'], true);

        return [
            'firma' => ['required', 'string'],
            'titularidad' => ['nullable', 'boolean'],
            'parentesco' => [Rule::requiredIf($requiresPoder), 'nullable', 'string', 'max:255'],
            'carta_poder' => [Rule::requiredIf($requiresPoder), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'evidence_root' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('Este cargo no está asignado a usted.');
    }

}
