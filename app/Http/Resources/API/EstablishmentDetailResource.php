<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstablishmentDetailResource extends JsonResource
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
                    'name' => $this->category->category_name
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
            ],
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'program_offerings' => ProgramOfferingResource::collection($this->whenLoaded('programOfferings')),
            'labels' => LabelResource::collection($this->whenLoaded('labels')),
            'logo_url' => $this->logo_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
