<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="MentionResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Informatique"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Computer Science specialization"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class MentionResourceSchema
{
}
