<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserRole"),
 *         description="User roles",
 *         example={"ROLE_USER"}
 *     ),
 *     @OA\Property(property="associated_establishment", type="integer", nullable=true, example=1, description="Associated establishment ID for ROLE_ESTABLISHMENT users"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="establishment", ref="#/components/schemas/EstablishmentBasic", description="Associated establishment details (if applicable)")
 * )
 *
 * @OA\Schema(
 *     schema="UserRole",
 *     type="string",
 *     enum={"ROLE_USER", "ROLE_ADMIN", "ROLE_ESTABLISHMENT"},
 *     description="Available user roles: ROLE_USER (regular users), ROLE_ADMIN (administrators), ROLE_ESTABLISHMENT (institution users)"
 * )
 *
 * @OA\Schema(
 *     schema="EstablishmentBasic",
 *     type="object",
 *     title="Basic Establishment Info",
 *     description="Basic establishment information for user resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Université d'Antananarivo"),
 *     @OA\Property(property="abbreviation", type="string", example="UA")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles,
            'associated_establishment' => $this->associated_establishment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'establishment' => $this->when(
                $this->associated_establishment && $this->relationLoaded('establishment'),
                function () {
                    return [
                        'id' => $this->establishment->id,
                        'name' => $this->establishment->name,
                        'abbreviation' => $this->establishment->abbreviation,
                    ];
                }
            ),
        ];
    }
}
