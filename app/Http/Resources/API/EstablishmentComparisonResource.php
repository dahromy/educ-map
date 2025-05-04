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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'indicators' => [
                'student_count' => $this->student_count,
                'success_rate' => $this->success_rate,
                'professional_insertion_rate' => $this->professional_insertion_rate,
            ],
            'tuition_fees' => count($tuitionFees) > 0 ? $tuitionFees : null,
            'program_durations' => count($programDurations) > 0 ? $programDurations : null,
        ];
    }
}
