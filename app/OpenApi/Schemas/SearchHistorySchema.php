<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="SearchHistory",
 *     title="Search History",
 *     description="User search history entry",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="search_query_text", type="string", example="Computer Science Programs"),
 *     @OA\Property(property="search_filters", type="object", example={"region": "Analamanga", "domain": "Computer Science"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SearchHistorySchema
{
    // This is just a placeholder class for OpenAPI annotations
}
