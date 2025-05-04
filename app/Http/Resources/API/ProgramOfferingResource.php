<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramOfferingResource extends JsonResource
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
            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'domain' => $this->whenLoaded('domain', function () {
                return [
                    'id' => $this->domain->id,
                    'name' => $this->domain->name,
                ];
            }),
            'grade' => $this->whenLoaded('grade', function () {
                return [
                    'id' => $this->grade->id,
                    'name' => $this->grade->name,
                    'level' => $this->grade->level,
                ];
            }),
            'mention' => $this->whenLoaded('mention', function () {
                return [
                    'id' => $this->mention->id,
                    'name' => $this->mention->name,
                ];
            }),
            'accreditations' => AccreditationResource::collection($this->whenLoaded('accreditations')),
            'tuition_fees_info' => $this->tuition_fees_info,
            'program_duration_info' => $this->program_duration_info,
        ];
    }
}
