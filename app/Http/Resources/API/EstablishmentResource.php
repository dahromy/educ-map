<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstablishmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->category_name,
                ];
            }),
            'location' => [
                'address' => $this->address,
                'region' => $this->region,
                'city' => $this->city,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'contact' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
            ],
            'indicators' => [
                'student_count' => $this->student_count,
                'success_rate' => $this->success_rate,
                'professional_insertion_rate' => $this->professional_insertion_rate,
                'first_habilitation_year' => $this->first_habilitation_year,
                'status' => $this->status,
                'international_partnerships' => $this->international_partnerships,
            ],
            'labels' => $this->whenLoaded('labels', function () {
                return $this->labels->map(function ($label) {
                    return [
                        'id' => $label->id,
                        'name' => $label->name,
                        'description' => $label->description,
                    ];
                });
            }),
            'programs_summary' => $this->whenLoaded('programOfferings', function () {
                $offerings = $this->programOfferings;
                return [
                    'total_programs' => $offerings->count(),
                    'domains_count' => $offerings->unique('domain_id')->count(),
                    'grades_offered' => $offerings->isNotEmpty()
                        ? $offerings->load('grade')->pluck('grade.name')->unique()->values()->toArray()
                        : [],
                    'departments_count' => $this->whenLoaded('departments', function () {
                        return $this->departments->count();
                    }, 0),
                ];
            }),
            'recent_accreditation' => $this->whenLoaded('programOfferings', function () {
                $recentAccreditation = $this->programOfferings
                    ->flatMap(function ($offering) {
                        return $offering->accreditations ?? collect();
                    })
                    ->where('is_recent', true)
                    ->first();

                return $recentAccreditation ? [
                    'has_recent' => true,
                    'accreditation_date' => $recentAccreditation->accreditation_date,
                ] : ['has_recent' => false];
            }),
            'logo_url' => $this->logo_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
