<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

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

            // Department validation rules
            'departments' => 'nullable|array',
            'departments.*.id' => 'nullable|exists:departments,id',
            'departments.*.name' => 'required_with:departments.*|string|max:255',
            'departments.*.abbreviation' => 'nullable|string|max:50',
            'departments.*.description' => 'nullable|string',

            // Program offering validation rules
            'departments.*.program_offerings' => 'nullable|array',
            'departments.*.program_offerings.*.id' => 'nullable|exists:program_offerings,id',
            'departments.*.program_offerings.*.domain_id' => 'required_with:departments.*.program_offerings.*|exists:domains,id',
            'departments.*.program_offerings.*.grade_id' => 'required_with:departments.*.program_offerings.*|exists:grades,id',
            'departments.*.program_offerings.*.mention_id' => 'required_with:departments.*.program_offerings.*|exists:mentions,id',
            'departments.*.program_offerings.*.tuition_fees_info' => 'nullable|string',
            'departments.*.program_offerings.*.program_duration_info' => 'nullable|string',

            // Direct program offering validation rules (establishment-level, no department)
            'program_offerings' => 'nullable|array',
            'program_offerings.*.id' => 'nullable|exists:program_offerings,id',
            'program_offerings.*.domain_id' => 'required_with:program_offerings.*|exists:domains,id',
            'program_offerings.*.grade_id' => 'required_with:program_offerings.*|exists:grades,id',
            'program_offerings.*.mention_id' => 'required_with:program_offerings.*|exists:mentions,id',
            'program_offerings.*.tuition_fees_info' => 'nullable|string',
            'program_offerings.*.program_duration_info' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $establishment = $this->route('establishment');

            // Validate that existing departments belong to this establishment
            if ($this->has('departments')) {
                foreach ($this->input('departments', []) as $index => $departmentData) {
                    if (isset($departmentData['id'])) {
                        $department = \App\Models\Department::find($departmentData['id']);
                        if ($department && $department->establishment_id !== $establishment->id) {
                            $validator->errors()->add(
                                "departments.{$index}.id",
                                'Department does not belong to this establishment.'
                            );
                        }

                        // Validate that existing program offerings belong to this establishment and department
                        if (isset($departmentData['program_offerings'])) {
                            foreach ($departmentData['program_offerings'] as $progIndex => $programData) {
                                if (isset($programData['id'])) {
                                    $program = \App\Models\ProgramOffering::find($programData['id']);
                                    if ($program) {
                                        if ($program->establishment_id !== $establishment->id) {
                                            $validator->errors()->add(
                                                "departments.{$index}.program_offerings.{$progIndex}.id",
                                                'Program offering does not belong to this establishment.'
                                            );
                                        }
                                        if ($program->department_id !== $department->id) {
                                            $validator->errors()->add(
                                                "departments.{$index}.program_offerings.{$progIndex}.id",
                                                'Program offering does not belong to this department.'
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Validate that existing direct program offerings belong to this establishment
            if ($this->has('program_offerings')) {
                foreach ($this->input('program_offerings', []) as $index => $programData) {
                    if (isset($programData['id'])) {
                        $program = \App\Models\ProgramOffering::find($programData['id']);
                        if ($program) {
                            if ($program->establishment_id !== $establishment->id) {
                                $validator->errors()->add(
                                    "program_offerings.{$index}.id",
                                    'Program offering does not belong to this establishment.'
                                );
                            }
                            // Ensure it's a direct program (no department)
                            if ($program->department_id !== null) {
                                $validator->errors()->add(
                                    "program_offerings.{$index}.id",
                                    'This program offering belongs to a department. Use departments.*.program_offerings instead.'
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}
