<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="DomainResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Computer Science"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Study of computers and computational systems"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class DomainResourceSchema
{
}
