<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     title="Pagination Links",
 *     description="Pagination links for API responses",
 *     @OA\Property(property="first", type="string", example="https://example.com/api/establishments?page=1"),
 *     @OA\Property(property="last", type="string", example="https://example.com/api/establishments?page=5"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", example="https://example.com/api/establishments?page=2")
 * )
 */
class PaginationLinksSchema
{
    // This is just a placeholder class for OpenAPI annotations
}
