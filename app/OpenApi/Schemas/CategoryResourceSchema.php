<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     title="Category Resource",
 *     description="Category resource for list view",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Public University"),
 *     @OA\Property(property="description", type="string", nullable=true, example="State-funded universities"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CategoryResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}
