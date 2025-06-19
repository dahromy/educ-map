<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('ROLE_ADMIN');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $gradeId = $this->route('grade')->id;

        return [
            'name' => 'sometimes|required|string|max:255|unique:grades,name,' . $gradeId . ',id',
            'level' => 'nullable|integer|min:1|max:10',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
