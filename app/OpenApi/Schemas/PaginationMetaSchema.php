<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="Pagination Meta",
 *     description="Pagination metadata for API responses",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="links", type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="url", type="string", nullable=true),
 *             @OA\Property(property="label", type="string"),
 *             @OA\Property(property="active", type="boolean")
 *         )
 *     ),
 *     @OA\Property(property="path", type="string", example="https://example.com/api/establishments"),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="to", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=75)
 * )
 */
class PaginationMetaSchema
{
    // This is just a placeholder class for OpenAPI annotations
}
