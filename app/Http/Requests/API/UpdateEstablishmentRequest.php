<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateEstablishmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $establishment = $this->route('establishment');
        return Gate::allows('update', $establishment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'abbreviation' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo_url' => 'nullable|url|max:255',
            'student_count' => 'nullable|integer|min:0',
            'success_rate' => 'nullable|numeric|between:0,100',
            'professional_insertion_rate' => 'nullable|numeric|between:0,100',
            'first_habilitation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
            'status' => 'nullable|string|in:private,public,semi-private',
            'international_partnerships' => 'nullable|string',
        ];
    }
}
