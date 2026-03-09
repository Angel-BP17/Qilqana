<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user?->can('users.edit')
            || $user?->hasRole('ADMINISTRADOR');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($this->route('user'))],
            'password' => 'nullable|string',
            'roles' => 'nullable|array',
            'roles.*' => ['required', 'string', Rule::exists('roles', 'name')],
        ];
    }
}
