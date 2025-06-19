<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="GradeResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Licence"),
 *     @OA\Property(property="level", type="integer", example=3),
 *     @OA\Property(property="description", type="string", nullable=true, example="Bachelor's degree level"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class GradeResourceSchema
{
}
