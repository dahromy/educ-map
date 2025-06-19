<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="LabelResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Excellence"),
 *     @OA\Property(property="color", type="string", example="#FF5733"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Label for excellent institutions"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class LabelResourceSchema
{
}
