<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class IndexEstablishmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint, no authorization required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'region' => 'sometimes|string|max:100',
            'category' => 'sometimes|string|max:100',
            'name' => 'sometimes|string|max:255',
            'abbreviation' => 'sometimes|string|max:50',
            'domain' => 'sometimes|string|max:255',
            'label' => 'sometimes|string|max:100',
            'reference_start_date' => 'sometimes|date',
            'reference_end_date' => 'sometimes|date|after_or_equal:reference_start_date',
            'sort_by' => 'sometimes|string|in:name,student_count,reference_date,success_rate,professional_insertion_rate,first_habilitation_year',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'has_recent_accreditation' => 'sometimes|in:true,false,1,0',
            'min_student_count' => 'sometimes|integer|min:0',
            'max_student_count' => 'sometimes|integer|min:0',
            'city' => 'sometimes|string|max:100',
            'q' => 'sometimes|string|max:255',
            'query' => 'sometimes|string|max:255',
        ];
    }
}
