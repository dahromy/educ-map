<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccreditationResource extends JsonResource
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
            'reference_type' => $this->reference_type,
            'accreditation_date' => $this->accreditation_date,
            'is_recent' => $this->is_recent,
            'reference' => $this->whenLoaded('reference', function () {
                return [
                    'id' => $this->reference->id,
                    'title' => $this->reference->title,
                    'main_date' => $this->reference->main_date,
                    'document_url' => $this->reference->document_url,
                ];
            }),
        ];
    }
}
