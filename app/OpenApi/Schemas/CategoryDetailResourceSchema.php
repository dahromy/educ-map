<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="CategoryDetailResource",
 *     title="Category Detail Resource",
 *     description="Detailed category resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Public University"),
 *     @OA\Property(property="description", type="string", nullable=true, example="State-funded universities"),
 *     @OA\Property(property="establishments_count", type="integer", nullable=true, example=15, description="Number of establishments in this category (included when include_establishments=true)"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CategoryDetailResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}
