<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('ROLE_ADMIN');
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|string|in:ROLE_USER,ROLE_ADMIN,ROLE_ESTABLISHMENT',
            'associated_establishment' => 'nullable|integer|exists:establishments,id|required_if:roles.*,ROLE_ESTABLISHMENT',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'associated_establishment.required_if' => 'An establishment must be selected when assigning the ROLE_ESTABLISHMENT role.',
            'roles.*.in' => 'Invalid role. Valid roles are: ROLE_USER, ROLE_ADMIN, ROLE_ESTABLISHMENT.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Ensure ROLE_ESTABLISHMENT users have an associated establishment
            if (in_array('ROLE_ESTABLISHMENT', $this->roles ?? []) && !$this->associated_establishment) {
                $validator->errors()->add('associated_establishment', 'An establishment must be selected for ROLE_ESTABLISHMENT users.');
            }

            // Ensure non-ROLE_ESTABLISHMENT users don't have an associated establishment
            if (!in_array('ROLE_ESTABLISHMENT', $this->roles ?? []) && $this->associated_establishment) {
                $validator->errors()->add('associated_establishment', 'Only ROLE_ESTABLISHMENT users can have an associated establishment.');
            }
        });
    }
}
