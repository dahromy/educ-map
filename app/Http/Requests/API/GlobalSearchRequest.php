<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class GlobalSearchRequest extends FormRequest
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
            // Text search
            'q' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'abbreviation' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            
            // Location filters
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            
            // Category and classification
            'category_id' => 'nullable|integer|exists:categories,id',
            'category_name' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            
            // Academic filters
            'domain_id' => 'nullable|integer|exists:domains,id',
            'domain_name' => 'nullable|string|max:100',
            'grade_id' => 'nullable|integer|exists:grades,id',
            'grade_name' => 'nullable|string|max:100',
            'mention_id' => 'nullable|integer|exists:mentions,id',
            'mention_name' => 'nullable|string|max:100',
            'label_id' => 'nullable|integer|exists:labels,id',
            'label_name' => 'nullable|string|max:100',
            
            // Indicators and metrics
            'student_count_min' => 'nullable|integer|min:0',
            'student_count_max' => 'nullable|integer|min:0',
            'success_rate_min' => 'nullable|numeric|min:0|max:100',
            'success_rate_max' => 'nullable|numeric|min:0|max:100',
            'professional_insertion_rate_min' => 'nullable|numeric|min:0|max:100',
            'professional_insertion_rate_max' => 'nullable|numeric|min:0|max:100',
            'first_habilitation_year_min' => 'nullable|integer|min:1900|max:' . date('Y'),
            'first_habilitation_year_max' => 'nullable|integer|min:1900|max:' . date('Y'),
            
            // Accreditation filters
            'has_recent_accreditation' => 'nullable|boolean',
            'accreditation_date_from' => 'nullable|date',
            'accreditation_date_to' => 'nullable|date|after_or_equal:accreditation_date_from',
            'reference_type' => 'nullable|string|max:100',
            
            // Program offerings
            'has_programs' => 'nullable|boolean',
            'program_count_min' => 'nullable|integer|min:0',
            'program_count_max' => 'nullable|integer|min:0',
            'tuition_fees' => 'nullable|string|max:255',
            'program_duration' => 'nullable|string|max:255',
            
            // Partnerships and features
            'has_international_partnerships' => 'nullable|boolean',
            'international_partnerships' => 'nullable|string|max:255',
            
            // Geographic filters
            'has_coordinates' => 'nullable|boolean',
            'latitude_min' => 'nullable|numeric|between:-90,90',
            'latitude_max' => 'nullable|numeric|between:-90,90',
            'longitude_min' => 'nullable|numeric|between:-180,180',
            'longitude_max' => 'nullable|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0|max:1000',
            'center_lat' => 'nullable|numeric|between:-90,90|required_with:radius_km',
            'center_lng' => 'nullable|numeric|between:-180,180|required_with:radius_km',
            
            // Contact information
            'has_email' => 'nullable|boolean',
            'has_phone' => 'nullable|boolean',
            'has_website' => 'nullable|boolean',
            'email_domain' => 'nullable|string|max:100',
            
            // Sorting and pagination
            'sort_by' => 'nullable|string|in:name,abbreviation,student_count,success_rate,professional_insertion_rate,first_habilitation_year,created_at,updated_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            
            // Include relationships
            'include' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'domain_id.exists' => 'The selected domain does not exist.',
            'grade_id.exists' => 'The selected grade does not exist.',
            'mention_id.exists' => 'The selected mention does not exist.',
            'label_id.exists' => 'The selected label does not exist.',
            'student_count_min.min' => 'Student count minimum must be at least 0.',
            'student_count_max.min' => 'Student count maximum must be at least 0.',
            'success_rate_min.between' => 'Success rate minimum must be between 0 and 100.',
            'success_rate_max.between' => 'Success rate maximum must be between 0 and 100.',
            'professional_insertion_rate_min.between' => 'Professional insertion rate minimum must be between 0 and 100.',
            'professional_insertion_rate_max.between' => 'Professional insertion rate maximum must be between 0 and 100.',
            'center_lat.required_with' => 'Center latitude is required when using radius search.',
            'center_lng.required_with' => 'Center longitude is required when using radius search.',
            'accreditation_date_to.after_or_equal' => 'End date must be after or equal to start date.',
            'per_page.max' => 'Maximum 100 results per page allowed.',
            'sort_by.in' => 'Invalid sort field. Allowed values: name, abbreviation, student_count, success_rate, professional_insertion_rate, first_habilitation_year, created_at, updated_at.',
            'sort_order.in' => 'Sort order must be either asc or desc.',
        ];
    }

    /**
     * Get the validated data with defaults.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        return array_merge([
            'sort_by' => 'name',
            'sort_order' => 'asc',
            'per_page' => 20,
            'page' => 1,
            'include' => '',
        ], $validated);
    }
}
