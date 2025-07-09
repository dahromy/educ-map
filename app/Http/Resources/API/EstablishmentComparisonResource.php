<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstablishmentComparisonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array
    {
        $programOfferings = $this->whenLoaded('programOfferings');

        // Calculate tuition fee ranges if program offerings are loaded
        $tuitionFees = [];
        if ($programOfferings) {
            foreach ($programOfferings as $offering) {
                if ($offering->tuition_fees_info) {
                    $tuitionFees[] = $offering->tuition_fees_info;
                }
            }
        }

        // Calculate program durations if program offerings are loaded
        $programDurations = [];
        if ($programOfferings) {
            foreach ($programOfferings as $offering) {
                if ($offering->program_duration_info) {
                    $programDurations[] = $offering->program_duration_info;
                }
            }
        }

        // Calculate domains and grades offered
        $domainsOffered = [];
        $gradesOffered = [];
        $departmentsCount = 0;
        $totalPrograms = 0;
        $recentAccreditation = null;

        if ($programOfferings) {
            $totalPrograms = $programOfferings->count();
            $departmentsCount = $programOfferings->unique('department_id')->count();
            
            // Get unique domains
            $uniqueDomains = $programOfferings->unique('domain_id');
            foreach ($uniqueDomains as $offering) {
                if ($offering->domain) {
                    $domainsOffered[] = $offering->domain->name;
                }
            }
            
            // Get unique grades
            $uniqueGrades = $programOfferings->unique('grade_id');
            foreach ($uniqueGrades as $offering) {
                if ($offering->grade) {
                    $gradesOffered[] = $offering->grade->name;
                }
            }
            
            // Check for recent accreditation
            $recentAccreditation = $programOfferings
                ->flatMap(function ($offering) {
                    return $offering->accreditations ?? collect();
                })
                ->where('is_recent', true)
                ->first();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            
            // Basic information
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->category_name,
                ];
            }),
            
            // Location details
            'location' => [
                'address' => $this->address,
                'region' => $this->region,
                'city' => $this->city,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            
            // Contact information
            'contact' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
            ],
            
            // Key indicators
            'indicators' => [
                'student_count' => $this->student_count,
                'success_rate' => $this->success_rate,
                'professional_insertion_rate' => $this->professional_insertion_rate,
                'first_habilitation_year' => $this->first_habilitation_year,
                'status' => $this->status,
                'international_partnerships' => $this->international_partnerships,
            ],
            
            // Academic offerings
            'academic_offerings' => [
                'total_programs' => $totalPrograms,
                'departments_count' => $departmentsCount,
                'domains_offered' => $domainsOffered,
                'grades_offered' => $gradesOffered,
                'tuition_fees' => count($tuitionFees) > 0 ? array_unique($tuitionFees) : null,
                'program_durations' => count($programDurations) > 0 ? array_unique($programDurations) : null,
            ],
            
            // Labels and certifications
            'labels' => $this->whenLoaded('labels', function () {
                return $this->labels->map(function ($label) {
                    return [
                        'id' => $label->id,
                        'name' => $label->name,
                        'color' => $label->color,
                        'description' => $label->description,
                    ];
                });
            }),
            
            // Recent accreditation status
            'recent_accreditation' => $recentAccreditation ? [
                'has_recent' => true,
                'accreditation_date' => $recentAccreditation->accreditation_date,
                'reference_type' => $recentAccreditation->reference_type,
            ] : ['has_recent' => false],
            
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
